<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatus;
use App\Models\BcCommandes;
use App\Models\BcUtilisateur;
use App\Models\Client;
use App\Models\Produits;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $commandes = BcCommandes::with(['client', 'conseiller']) // chargement anticipé des relations
            ->when($request->client_id, function ($query, $client_id) { // active la fonction quand client_id est requêter
                $query->where('client_id', $client_id); // ajoute le filtrage si applicable
            })
            ->when(Auth::user()->role === 'revendeur', function ($query) { // applique le filtre si l'utilisateur a le role revendeur, l'admin peut tout voir
                $query->where('conseiller_id', Auth::user()->id); // montre que les commandes du conseiller connecté
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
            //'modalite_paiement' => 'required|string',
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
            'numero_commande' => 'CMD-' . strtoupper(uniqid()), // génère un num de commande unique
            'client_id' => $request->client_id,
            'conseiller_id' => Auth::user()->id,
            'date_commande' => now(),
            'modalites_paiement' => null,
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
    
        // générer les pdf
        $pdfdetail = $commande->load('client', 'produits', 'conseiller');
        $orderPdf = PDF::loadView('orders.purchaseOrder', ['commande' => $pdfdetail]);
        $cgvPdf = PDF::loadView('orders.cgv')->output();

        // sauvegarde chacun des pdf
        $orderPdfPath = public_path('bc/' . $commande->numero_commande . '.pdf');
        $orderPdf->save($orderPdfPath);

        $cgvPdfPath = public_path('bc/cgv-' . $commande->numero_commande . '.pdf');
        file_put_contents($cgvPdfPath, $cgvPdf);

        // fusion des pdf
        $finalPdfPath = public_path('bc/' . $commande->numero_commande . '.pdf');
        $pdfMerger = PDFMergerFacade::init();
        $pdfMerger->addPDF($orderPdfPath, 'all');
        $pdfMerger->addPDF($cgvPdfPath, 'all');
        $pdfMerger->merge();
        $pdfMerger->save($finalPdfPath);

        // ajout de la pagination sur le pdf
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($finalPdfPath);
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($tplId);
            
            // ajouter la page avec son contenu
            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($tplId, 0, 0, null, null, true);
            
            // Configuration pour la pagination
            $pdf->SetFont('Helvetica', '', 6);
            $pdf->SetY(-5);  // position à 15mm du bas
            $pdf->SetX(-15); // position à 30mm de la droite
            $pdf->SetTextColor(0, 0, 0);
            
            // Rectangle blanc derrière le texte pour éviter la superposition
            $pdf->SetFillColor(255, 255, 255);
            $text = 'Page ' . $pageNo . ' / ' . $pageCount;
            $textWidth = $pdf->GetStringWidth($text);
            $pdf->Cell($textWidth, 5, $text, 0, 0, 'R', true);
        }
        
        $pdf->Output('F', $finalPdfPath);

        // envoyer le mail à la création de la commande
        $emailClient = $commande->client->email; 
        Mail::to($emailClient)->send(new OrderStatus($commande, $finalPdfPath));

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
    
        // validation des données du formulaire
        $validatedData = $request->validate([
            'modalite_paiement' => 'required|string|in:prelevement,virement,cheque',
            'planification' => 'required|string|in:annuel,trimestriel,semestriel,mensuel',
            'iban' => 'required_if:modalite_paiement,prelevement|nullable|string|max:34',
            'bic' => 'required_if:modalite_paiement,prelevement|nullable|string|max:11',
            'authorization' => 'required_if:modalite_paiement,prelevement|boolean',
            'is_cgv_validated' => 'required|boolean',
        ]);

        // verif si la checkbox est coché
        if (!$request->has('is_cgv_validated') || !$request->is_cgv_validated) {
            return redirect()->back()->with('error', 'Vous devez accepter les CGV pour continuer.');
        }

        // met à jour les modalité de paiement dans `bc_commandes`
        $commande->update([
            'modalites_paiement' => $validatedData['modalite_paiement'],
            'planification' => $validatedData['planification'], 
            'is_cgv_validated' => true,
            'validatedAt' => now(),
        ]);

        // si "Prélèvement" a été sélectionné on ajoute les informations dans bc_mandats
        if ($validatedData['modalite_paiement'] === 'prelevement') {
            $referenceUnique = 'MANDAT-' . strtoupper(uniqid());
    
            // insertion dans la table bc_mandats
            DB::table('bc_mandats')->insert([
                'commande_id' => $commande->id,
                'reference_unique' => $referenceUnique,
                'iban' => $validatedData['iban'],
                'bic' => $validatedData['bic'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Commande validée avec succès.');
    }


    public function processedOrder(Request $request, BcCommandes $commande)
    {
        $commande->update([
            'isProcessed' => $request->isProcessed,
        ]);

        return redirect()->route('orders.index')->with('success', 'Commande traitée avec succès');
    }
}
