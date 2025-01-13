<?php

namespace App\Http\Controllers;

use App\Models\BcCommandes;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripeController extends Controller
{
    // montant minimum et maximum en centime
    private const MIN_AMOUNT = 100; // 1€
    private const MAX_AMOUNT = 1000000; // 10000€

    public function createCheckoutSession(Request $request)
    {
        try {
            // valider les données
            $validated = $request->validate([
                'commande_id' => 'required|integer|exists:bc_commandes,id',
                'amount' => 'required|integer|min:' . self::MIN_AMOUNT . '|max:' . self::MAX_AMOUNT,
                'planification' => 'nullable|string|in:annuel,trimestriel,semestriel,mensuel',
                'is_cgv_validated' => 'required|boolean|accepted',
                'token' => 'required|string' // token envoyé dans l'email
            ]);

            $commande = BcCommandes::findOrFail($validated['commande_id']);
            if ($commande->modalites_paiement !== 'prelevement') {
                $validated['planification'] = null;
            }

            // récupération de la commande avec vérification du token
            $commande = BcCommandes::where('id', $validated['commande_id'])
                ->where('payment_token', $validated['token']) // token unique par commande
                ->where('payment_link_expires_at', '>', now()) // vérifier que le lien n'a pas expiré
                ->firstOrFail();

            // vérifie que la commande n'a pas déjà été payée
            if ($commande->isPaid()) {
                throw new \Exception('Cette commande a déjà été payée.');
            }

            // génération d'un idempotency key
            $idempotencyKey = Str::uuid()->toString();

            //maj de la commande
            $commande->update(array_merge($validated, [
                'modalites_paiement' => 'virement',
                'validatedAt' => now(),
                'last_payment_attempt' => now(),
                'payment_session_id' => $idempotencyKey
            ]));

                    // Log pour voir l'état avant d'envoyer à Stripe
        Log::info('Commande mise à jour pour Stripe', [
            'commande_id' => $commande->id,
            'amount' => $validated['amount'],
            'planification' => $validated['planification'],
            'client_email' => $commande->client_email
        ]);

            // configuration de Stripe
            if (!env('STRIPE_SECRET')) {
                throw new \Exception('Configuration Stripe manquante.');
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));
            Stripe::setApiVersion('2023-10-16');

            // création de la session Stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Commande ' . $commande->numero_commande,
                            'description' => 'Paiement pour la commande #' . $commande->numero_commande,
                            'metadata' => [
                                'commande_id' => $commande->id,
                                'email' => $commande->client_email,
                            ],
                        ],
                        'unit_amount' => $validated['amount'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'client_reference_id' => $commande->id,
                'customer_email' => $commande->client_email,
                'metadata' => [
                    'commande_id' => $commande->id,
                    'payment_token' => $validated['token']
                ],
                'success_url' => route('orders.finishedCgv', [
                    'commande' => $commande->id,
                    'token' => $validated['token'],
                    'session_id' => $idempotencyKey,
                    'success' => 'true' 
                ]),
                'cancel_url' => route('orders.confirm', [
                    'commande' => $commande->id,
                    'token' => $validated['token']
                ]),
            ], ['idempotency_key' => $idempotencyKey]);

            return response()->json([
                'id' => $session->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur Stripe', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => 'une erreur est survenue lors de la création de la session de paiement.'
            ], 500);
        }
    }
}