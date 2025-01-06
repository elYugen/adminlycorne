@extends('layouts.base')
@section('title', 'Gestion commandes')

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Gestion des commandes</h1>

    <!-- Message de succès -->
    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bouton pour créer une commande -->
    <div class="mt-4 mb-4">
        <a href="" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Créer une commande
        </a>
    </div>

    <!-- Filtre par client -->
    <form action="{{ route('orders.index') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-8">
                <select name="client_id" class="form-select">
                    <option value="">Liste des clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </div>
    </form>

    <!-- Tableau des commandes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Conseiller</th>
                <th>Date</th>
                <th>Total TTC</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commandes as $commande)
            <tr>
                <td>{{ $commande->numero_commande }}</td>
                <td>{{ $commande->client->nom }}</td>
                <td>{{ $commande->conseiller->name }}</td>
                <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                <td>{{ number_format($commande->total_ttc, 2, ',', ' ') }} €</td>
                <td>
                    <a href="{{ route('orders.show', $commande->id) }}" class="btn btn-sm btn-primary">Voir</a>
                    <a href="{{ route('orders.edit', $commande->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucune commande trouvée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $commandes->links() }}
</div>
@endsection