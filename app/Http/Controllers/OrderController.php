<?php

namespace App\Http\Controllers;

use App\Models\BcCommandes;
use App\Models\BcUtilisateur;
use App\Models\Client;
use App\Models\Produits;
use App\Models\Prospect;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $commandes = BcCommandes::with(['client', 'conseiller']) // chargement anticipé des relations
            ->when($request->client_id, function ($query, $client_id) { // active la fonction quand client_id est requêter
                $query->where('client_id', $client_id); // ajoute le filtrage si applicable
            })
            ->paginate(10);
    
        $clients = Prospect::all();
        $produits = Produits::all();
        $conseillers = BcUtilisateur::select('id', 'name')->get();

        return view('orders.index', compact('commandes', 'clients', 'produits', 'conseillers'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:bc_prospects,id',
            'conseiller_id' => 'required|exists:bc_conseillers,id',
            'date_commande' => 'required|date',
            'modalite_paiement' => 'required|string',
            'produits' => 'required|array',
            'produits.*.produit_id' => 'required|exists:bc_produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
            'produits.*.prix_ht' => 'required|numeric|min:0',
        ]);
    
        $totalHT = 0;
        foreach ($request->produits as $produit) {
            $totalHT += $produit['quantite'] * $produit['prix_ht'];
        }
        $totalTTC = $totalHT * 1.2; // TVA à 20%
    
        $commande = BcCommandes::create([
            'numero_commande' => 'CMD-' . strtoupper(uniqid()),
            'client_id' => $request->client_id,
            'conseiller_id' => $request->conseiller_id,
            'date_commande' => $request->date_commande,
            'modalites_paiement' => $request->modalite_paiement,
            'total_ht' => $totalHT,
            'total_ttc' => $totalTTC,
        ]);
    
        foreach ($request->produits as $produit) {
            $commande->produits()->attach($produit['produit_id'], [
                'quantite' => $produit['quantite'],
                'prix_ht' => $produit['prix_ht'],
            ]);
        }
    
        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');
    }

}
