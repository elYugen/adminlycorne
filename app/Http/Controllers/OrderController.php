<?php

namespace App\Http\Controllers;

use App\Models\BcCommandes;
use App\Models\Client;
use App\Models\Produits;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $commandes = BcCommandes::with(['client', 'conseiller'])
            ->when($request->client_id, function ($query, $client_id) {
                $query->where('client_id', $client_id);
            })
            ->paginate(10);
    
        $clients = Client::all();
        $produits = Produits::all();

        return view('orders.index', compact('commandes', 'clients', 'produits'));
    }

}
