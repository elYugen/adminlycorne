@extends('layouts.base')
@section('title', 'Gestion commandes')

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Gestion des commandes</h1>

    <!-- gestion de succès et erreur -->
    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="mt-4 mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#commandeModal">
            <i class="bi bi-person-plus"></i> Créer une commande
        </button>
    </div>

    <!-- filtrer par client -->
    <form action="{{ route('orders.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-8">
                <select name="client_id" class="form-select">
                    <option value="">Liste des clients ---></option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->firstname }} {{ $client->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </div>
    </form>
    
    <!-- tableau des commandes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Date</th>
                <th>Produit</th>
                <th>Total HT</th>
                <th>Total TTC</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commandes as $commande)
            <tr>
                <td>{{ $commande->numero_commande }}</td>
                <td>{{ $commande->client->firstname }} {{ $commande->client->lastname }}</td>
                <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                <td>
                    @foreach($commande->produits as $produit)
                        {{ $produit->nom }} ({{ $produit->pivot->quantite }} x {{ $produit->prix_ht }} € HT) <br>
                    @endforeach
                </td>
                <td>{{ $commande->total_ht }} € HT</td>
                <td>{{ $commande->total_ttc }} € TTC</td>
                <td>
                    <form action="{{ route('orders.processed', $commande->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="isProcessed" value="1">
                        <button type="submit" class="btn btn-warning btn-sm">Traité</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucune commande trouvée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    {{ $commandes->links() }}
</div>
@endsection

@include('layouts.components.order_modal')