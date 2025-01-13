@extends('layouts.base')
@section('title', 'Gestion des commandes')

@section('styles')
<!-- CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Gestion des bons de commandes</h1>

    <!-- Message de succès -->
    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

    <div class="mt-4 mb-4">
        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#commandeModal" style="background-color: #b7b7c5; color: white;">
            <i class="bi bi-person-plus"></i> Ajouter une commande
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover commandes-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Produits</th>
                    <th>Total HT</th>
                    <th>Total TTC</th>
                    <th>Validé le</th>
                    <th>Traité</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($commandes as $commande)
                <tr>
                    <td>{{ $commande->numero_commande }}</td>
                    <td>{{ $commande->client->firstname }} {{ $commande->client->lastname }}</td>
                    <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                    <td>
                        @foreach ($commande->produits as $produit)
                            {{ $produit->nom }} ({{ $produit->pivot->quantite }} x {{ $produit->prix_ht }} € HT)<br>
                        @endforeach
                    </td>
                    <td>{{ $commande->total_ht }} €</td>
                    <td>{{ $commande->total_ttc }} €</td>
                    <td>{{ $commande->validatedAt ? \Carbon\Carbon::parse($commande->validatedAt)->format('d/m/Y') : 'Non validé' }}</td>
                    <td>
                        @if($commande->isProcessed)
                            Traité
                        @else
                            <div class="d-flex align-items-center gap-1">
                                Non traité
                                <form action="{{ route('orders.processed', $commande->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="isProcessed" value="1">
                                    <button type="submit" class="btn btn-sm"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('layouts.components.order_modal')
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('.commandes-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [7] },
                { 
                targets: 2, // cible la colonne date
                type: 'date-eu' // type de date au format europe
                }
            ],
            order: [[2, 'desc']],
            pageLength: 10, // nombre max de ligne sur la pagination
        });
    });
</script>
@endsection
@include('layouts.components.order_modal')