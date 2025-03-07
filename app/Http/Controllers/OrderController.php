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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Webklex\PDFMerger\Facades\PDFMergerFacade;


class OrderController extends Controller
{
    public function index()
    {
        $commandesQuery = BcCommandes::active()->with(['client', 'produits', 'conseiller'])
            ->when(Auth::user()->role === 'revendeur', function ($query) {
                $query->where('conseiller_id', Auth::user()->id);
            });
                
        $commandes = $commandesQuery->get();
        $clients = Prospect::all();
        $produits = Produits::all();
        $conseillers = BcUtilisateur::select('id', 'name')->get();
    
        return view('orders.index', compact('commandes', 'clients', 'produits', 'conseillers'));
    }

    public function delete(BcCommandes $commande)
    {
        $commande->update(['deleted' => 1]);
        return redirect()->route('orders.index')->with('success', 'Commande supprimée avec succès');
    }

    public function create()
    {
        $clients = Prospect::all();
        $produits = Produits::all();
        $conseillers = BcUtilisateur::select('id', 'name')->get();
    
        $old = session()->getOldInput();
        $oldProduits = [];
    
        return view('orders.create', compact('clients', 'produits', 'conseillers', 'old', 'oldProduits'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:bc_prospects,id',
            //'modalite_paiement' => 'required|string',
            'produits' => 'required|array',
            'produits.*.produit_id' => 'required|exists:bc_produits,id',
            'produits.*.quantite' => 'required|regex:/^\d*[,.]?\d{0,2}$/',
            'produits.*.prix_ht' => 'required|numeric|min:0',
        ]);
    
        // convertir les quantités du format français au format standard
        $produits = collect($request->produits)->map(function ($produit) {
            $produit['quantite'] = (float) str_replace(',', '.', str_replace(' ', '', $produit['quantite']));
            return $produit;
        })->toArray();
    
        $totalHT = 0;
        foreach ($produits as $produit) {
            $totalHT += $produit['quantite'] * $produit['prix_ht'];
        }
        $totalTTC = $totalHT * 1.2; // TVA à 20%
    
        // génère un token unique et une date d'expiration
        $paymentToken = Str::random(64);
        $expiresAt = now()->addDays(7); // Le lien expire dans 7 jours

        $commande = BcCommandes::create([
            'numero_commande' => 'CMD-' . strtoupper(uniqid()), // génère un num de commande unique
            'client_id' => $request->client_id,
            'conseiller_id' => Auth::user()->id,
            'date_commande' => now(),
            'modalites_paiement' => null,
            'total_ht' => $totalHT,
            'total_ttc' => $totalTTC,
            'payment_token' => $paymentToken,
            'payment_link_expires_at' => $expiresAt,
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
            $pdf->SetY(1);  // position à 1mm du bas
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
        Mail::to($emailClient)->send(new OrderStatus($commande, $finalPdfPath, $paymentToken));

        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');
    }

    public function showCgv(BcCommandes $commande, Request $request)
    {
        if (!$commande) { // verif si la commande existe
            return redirect()->route('orders.index')->with('error', 'Commande introuvable.');
        }

        // récupération du token de l'URL
        $urlToken = $request->query('token');

        // verifie que le token est présent dans l'url et correspond à celui de la commande
        if (!$urlToken || $urlToken !== $commande->payment_token) {
            return redirect()->route('orders.index')->with('error', 'Lien de paiement invalide.');
        }

        // vérifier la date d'expiration du lien
        if ($commande->payment_link_expires_at && $commande->payment_link_expires_at < now()) {
            return redirect()->route('orders.index')->with('error', 'Le lien de paiement a expiré.');
        }

        // envoyer le token de la base de données à la vue
        $token = $commande->payment_token;

        return view('mail.orderConfirm', compact('commande', 'token'));
    }

    public function validateCgv(Request $request, BcCommandes $commande)
    {
        if (!$commande) { // verif si la commande existe
            return redirect()->route('orders.index')->with('error', 'Commande introuvable.');
        }

        // validation des données du formulaire
        $validatedData = $request->validate([
            'modalite_paiement' => 'required|string|in:prelevement,virement,cheque',
            'planification' => 'nullable|string|in:annuel,trimestriel,semestriel,mensuel',
            'iban' => 'nullable|required_if:modalite_paiement,prelevement|string|max:34',
            'bic' => 'nullable|required_if:modalite_paiement,prelevement|string|max:11',
            'authorization' => 'nullable|required_if:modalite_paiement,prelevement|boolean',
            'is_cgv_validated' => 'required|boolean',
        ]);

        // verif si la checkbox est coché
        if (!$request->has('is_cgv_validated') || !$request->is_cgv_validated) {
            return redirect()->back()->with('error', 'Vous devez accepter les CGV pour continuer.');
        }

        // retire planification si la modalite de paiement n'est pas prelevement
        if ($validatedData['modalite_paiement'] !== 'prelevement') {
            $validatedData['planification'] = null;
        }

        // met à jour les modalité de paiement dans `bc_commandes`
        $commande->update([
            'modalites_paiement' => $validatedData['modalite_paiement'],
            'planification' => $validatedData['planification'] ?? null, 
            'is_cgv_validated' => true,
            'validatedAt' => now(),
        ]);

        // si prélèvement est sélectionné on ajoute les informations dans bc_mandats
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

        return redirect()->route('orders.finishedCgv', ['commande' => $commande->id])->with('success', 'Commande validée avec succès.');
    }

    public function finishedCgv(Request $request, BcCommandes $commande)
    {
        if (!$commande) { // verif si la commande existe
            return redirect()->route('orders.index')->with('error', 'Commande introuvable.');
        }

        Log::info('Paramètres reçus:', [
            'success' => $request->success,
            'has_success' => $request->has('success'),
            'all_parameters' => $request->all()
        ]);

        // verifie si l'url à le mot clé "success"
        //if ($request->has('success') && ($request->success == 'true' || $request->success === true)) {
            //session()->flash('success', 'Commande validée avec succès.');
        //}
        if ($request->has('success') && ($request->success == 'true' || $request->success === true)) {
            return redirect()->route('orders.index')->with('success', 'Commande validée avec succès.');
        }

        return view('mail.orderFinished', compact('commande'));
    }

    public function processedOrder(Request $request, BcCommandes $commande)
    {
        $commande->update([
            'isProcessed' => $request->isProcessed,
        ]);

        return redirect()->route('orders.index')->with('success', 'Commande traitée avec succès');
    }
}
