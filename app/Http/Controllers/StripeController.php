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
            // valide les données du formulaire
            $validated = $request->validate([
                'commande_id' => 'required|exists:bc_commandes,id',
                'amount' => 'required|integer|min:1',
            ]);

            // récupère les infos de la commande
            $commande = BcCommandes::findOrFail($validated['commande_id']);

            // initialise les config de stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // création de la session stripe
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Commande ' . $commande->numero_commande,
                        ],
                        'unit_amount' => $validated['amount'], // montant en centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('orders.finishedCgv', ['commande' => $commande->id]) . '?success=true',
                'cancel_url' => route('orders.confirm', ['commande' => $request->commande_id]),
            ]);

            // envoie une réponse json
            return response()->json(['id' => $session->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}