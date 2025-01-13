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
    private const MIN_AMOUNT = 100; // 1€
    private const MAX_AMOUNT = 1000000; // 10000€
    
    // Définir les options de paiement en plusieurs fois disponibles
    private const INSTALLMENT_OPTIONS = [
        '3' => 3,  // 3 fois
        '4' => 4,  // 4 fois
        '6' => 6,  // 6 fois
        '12' => 12 // 12 fois
    ];

    public function createCheckoutSession(Request $request)
    {
        try {
            Log::info('Données reçues', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'commande_id' => 'required|integer|exists:bc_commandes,id',
                'amount' => 'required|integer|min:' . self::MIN_AMOUNT . '|max:' . self::MAX_AMOUNT,
                'planification' => 'nullable|string|in:annuel,trimestriel,semestriel,mensuel',
                'is_cgv_validated' => 'required|boolean|accepted',
                'token' => 'required|string',
                'installments' => 'nullable|string|in:' . implode(',', array_keys(self::INSTALLMENT_OPTIONS))
            ]);

            Log::info('Données validées', [
                'validated_data' => $validated
            ]);

            $commande = BcCommandes::where('id', $validated['commande_id'])
                ->where('payment_token', $validated['token'])
                ->where('payment_link_expires_at', '>', now())
                ->firstOrFail();

            if ($commande->isPaid()) {
                throw new \Exception('Cette commande a déjà été payée.');
            }

            $idempotencyKey = Str::uuid()->toString();

            $commande->update(array_merge($validated, [
                'modalites_paiement' => 'virement',
                'validatedAt' => now(),
                'last_payment_attempt' => now(),
                'payment_session_id' => $idempotencyKey,
                'installments_count' => !empty($validated['installments']) 
                ? self::INSTALLMENT_OPTIONS[$validated['installments']] 
                : null
            ]));

            if (!env('STRIPE_SECRET')) {
                throw new \Exception('Configuration Stripe manquante.');
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));
            Stripe::setApiVersion('2023-10-16');
            $account = \Stripe\Account::retrieve();
            Log::info('Capacités du compte Stripe', [
                'capabilities' => $account->capabilities
            ]);

            // Configuration de base pour la session Stripe
            $sessionConfig = [
                'payment_method_types' => ['card'],
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
                ])
            ];

        // si paiement en plusieurs fois
        if (!empty($validated['installments'])) {
            $numberOfInstallments = self::INSTALLMENT_OPTIONS[$validated['installments']];
            
            // modification de la configuration pour les paiements échelonnés
            $sessionConfig['mode'] = 'payment';
            $sessionConfig['payment_method_options'] = [
                'card' => [
                    'installments' => [
                        'enabled' => true
                    ]
                ]
            ];
        }

            // Ajout des informations de produit
            $sessionConfig['line_items'] = [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Commande ' . $commande->numero_commande,
                        'description' => 'Paiement' . 
                            (!empty($validated['installments']) ? ' en ' . self::INSTALLMENT_OPTIONS[$validated['installments']] . ' fois' : '') . 
                            ' pour la commande #' . $commande->numero_commande,
                        'metadata' => [
                            'commande_id' => $commande->id,
                            'email' => $commande->client_email,
                        ],
                    ],
                    'unit_amount' => $validated['amount'],
                ],
                'quantity' => 1,
            ]];

            $sessionConfig['mode'] = 'payment';

            Log::info('Configuration Stripe', [
                'config' => $sessionConfig
            ]); 

            $session = Session::create($sessionConfig, ['idempotency_key' => $idempotencyKey]);

            return response()->json([
                'id' => $session->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur Stripe détaillée', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la création de la session de paiement.'
            ], 500);
        }
    }
}