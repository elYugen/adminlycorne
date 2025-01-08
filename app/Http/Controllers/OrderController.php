<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatus;
use App\Models\BcCommandes;
use App\Models\BcUtilisateur;
use App\Models\Client;
use App\Models\Produits;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $commandes = BcCommandes::with(['client', 'conseiller']) // chargement anticipé des relations
            ->when($request->client_id, function ($query, $client_id) { // active la fonction quand client_id est requêter
                $query->where('client_id', $client_id); // ajoute le filtrage si applicable
            })
            ->where('isProcessed', 0) // retire les commandes avec isProcessed a 1
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
            'conseiller_id' => 'required|exists:bc_utilisateurs,id',
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
            'numero_commande' => 'CMD-' . strtoupper(uniqid()), // génère un num de commande aléatoire
            'client_id' => $request->client_id,
            'conseiller_id' => $request->conseiller_id,
            'date_commande' => now(),
            'modalites_paiement' => $request->modalite_paiement,
            'total_ht' => $totalHT,
            'total_ttc' => $totalTTC,
        ]);
    
        foreach ($request->produits as $produit) {
            // recup le prix unitaire du produit dans la base de donnée
            $produitModel = Produits::findOrFail($produit['produit_id']);
            $prixUnitaireHT = $produitModel->prix_ht; //prix de base du produit
        
            // calcul le prix total ht pour le produit dans la commande
            $prixTotalHT = $prixUnitaireHT * $produit['quantite'];
        
            // insere dans la table pivot
            $commande->produits()->attach($produit['produit_id'], [
                'quantite' => $produit['quantite'],
                'prix_unitaire_ht' => $prixUnitaireHT,
                'prix_ht' => $prixTotalHT,
            ]);
        }
    
        // génération du pdf
        $pdfdetail = $commande->load('client', 'produits');
        $pdf = PDF::loadView('orders.purchaseOrder', ['commande' => $pdfdetail ]);
        $saveTo = public_path('bc/' . $commande->numero_commande . '.pdf');
        $pdf->save($saveTo);

        // envoie de mail après la création de la commande
        $emailClient = $commande->client->email; 
        Mail::to($emailClient)->send(new OrderStatus($commande, $saveTo));

        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');
    }

    public function showCgv(BcCommandes $commande)
    {
        if (!$commande) { // verif si la commande existe
            return redirect()->route('orders.index')->with('error', 'Commande introuvable.');
        }

        return view('mail.orderConfirm', compact('commande'));
    }

    public function validateCgv(Request $request, BcCommandes $commande)
    {
        if (!$commande) { // verif si la commande existe
            return redirect()->route('orders.index')->with('error', 'Commande introuvable.');
        }
    
        // verif si la checkbox est on
        if ($request->has('is_cgv_validated')) {
            // verif si les cgv ne sont pas déjà valide
            if (!$commande->is_cgv_validated) {
                $commande->update([
                    'is_cgv_validated' => true,
                    'validatedAt' => now(),
                ]);
    
                return redirect()->route('orders.index')->with('success', 'CGV validées avec succès.');
            }
    
            return redirect()->route('orders.index')->with('info', 'CGV déjà validées.');
        }
    
        return redirect()->route('orders.index')->with('error', 'Vous devez accepter les CGV pour continuer.');
    }


    public function processedOrder(Request $request, BcCommandes $commande)
    {
        $commande->update([
            'isProcessed' => $request->isProcessed,
        ]);

        return redirect()->route('orders.index')->with('success', 'Commande traitée avec succès');
    }
}
