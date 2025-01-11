<?php

namespace App\Http\Controllers;

use App\Models\BcCommandes;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    private const MIN_AMOUNT = 100; // montant minimum
    private const MAX_AMOUNT = 1000000; // montant max

    public function createCheckoutSession(Request $request)
    {
        try {
            // validation des données
            $validated = $request->validate([
                'commande_id' => 'required|exists:bc_commandes,id',
                'amount' => 'required|integer|min:' . self::MIN_AMOUNT . '|max:' . self::MAX_AMOUNT,
                'planification' => 'required|string|in:annuel,trimestriel,semestriel,mensuel',
                'is_cgv_validated' => 'required|boolean'
            ]);

            // récupération de la commande
            $commande = BcCommandes::findOrFail($validated['commande_id']);
            
            // maj de la commande avec les informations de planification et cgv validé
            $commande->update([
                'modalites_paiement' => 'virement', // carte bancaire
                'planification' => $validated['planification'],
                'is_cgv_validated' => true,
                'validatedAt' => now(),
            ]);

            // initialisation de stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // création de la session Stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Commande ' . $commande->numero_commande,
                        ],
                        'unit_amount' => $validated['amount'],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('orders.finishedCgv', ['commande' => $commande->id]) . '?success=true',
                'cancel_url' => route('orders.confirm', ['commande' => $request->commande_id]),
            ]);

            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}