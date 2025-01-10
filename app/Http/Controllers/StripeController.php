<?php

namespace App\Http\Controllers;

use App\Models\BcCommandes;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'commande_id' => 'required|exists:bc_commandes,id',
                'amount' => 'required|integer|min:1',
                'planification' => 'required|string|in:annuel,trimestriel,semestriel,mensuel',
                'is_cgv_validated' => 'required|boolean'
            ]);

            // Récupération de la commande
            $commande = BcCommandes::findOrFail($validated['commande_id']);
            
            // Mise à jour de la commande avec les informations de planification et CGV
            $commande->update([
                'modalites_paiement' => 'virement', // carte bancaire
                'planification' => $validated['planification'],
                'is_cgv_validated' => true,
                'validatedAt' => now(),
            ]);

            // Initialisation de Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Création de la session Stripe
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