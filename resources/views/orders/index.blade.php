@extends('layouts.base')
@section('title', 'Gestion des bons de commandes')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .page-item.active .page-link {
        background-color: #133A3F !important;
        border-color: #133A3F !important;
        color: white !important;
    }
    
    .page-link {
        color: #133A3F !important;
    }
    
    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        color: black !important;
    }
    
    .page-link:hover {
        background-color: #f8f9fa;
    }
    
    .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(19, 58, 63, 0.25);
    }
    .validation-column {
        min-width: 100px;
    }
    .order-column {
        min-width: 200px;
    }
</style>
@endsection

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Gestion des bons de commandes</h1>

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
        <a href="{{ route('orders.create') }}" class="btn" style="background-color: #205558; color: white;">
            <i class="bi bi-newspaper"></i> Créer un bon de commande
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover commandes-table">
            <thead>
                <tr>
                    <th class="order-column">#</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Produits</th>
                    <th>Total HT</th>
                    <th>Total TTC</th>
                    <th class="validation-column">Validé le</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($commandes as $commande)
                <tr>
                    <td>{{ $commande->numero_commande }}                         
                        <a href="{{ asset('bc/' . $commande->numero_commande . '.pdf') }}" target="_blank" class="btn btn-sm bg-transparent p-0 border-0" data-bs-toggle="tooltip" title="Voir le PDF">
                            <i class="bi bi-file-pdf" style="color: #133a3f; font-size: 1.2rem;"></i>
                        </a>
                    </td>
                    <td>{{ $commande->client->firstname }} {{ $commande->client->lastname }}</td>
                    <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</td>
                    <td>
                        @foreach ($commande->produits as $produit)
                            {{ $produit->nom }} ({{ $produit->pivot->quantite }} x {{ $produit->prix_ht }} € HT)<br>
                        @endforeach
                    </td>
                    <td>{{ $commande->total_ht }} €</td>
                    <td>{{ $commande->total_ttc }} €</td>
                    <td>
                        @if($commande->validatedAt)
                            {{ \Carbon\Carbon::parse($commande->validatedAt)->format('d/m/Y') }}
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Non validé</span>
                                <a href="{{ route('orders.showCgv', $commande->id) . '?token=' . $commande->payment_token }}" class="text-decoration-none text-info" onclick="event.stopPropagation();" target="_blank" data-bs-toggle="tooltip" title="Valider la commande"><i class="bi bi-file-text" style="color: #133a3f;"></i></a>
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($commande->isProcessed)
                            Commande Traitée
                        @else
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Non traité</span>
                                <form action="{{ route('orders.processed', $commande->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="isProcessed" value="1">
                                    <button type="submit" class="btn btn-sm bg-transparent p-0 border-0 text-decoration-none" data-bs-toggle="tooltip" title="Traiter la commande">
                                        <i class="bi bi-check-circle-fill" style="color: #133a3f; font-size: 0.9rem;"></i>
                                    </button>
                                </form>
                                <form action="{{ route('orders.delete', $commande->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm bg-transparent p-0 border-0 text-decoration-none" data-bs-toggle="tooltip" title="Supprimer la commande">
                                        <i class="bi bi-trash" style="color: #dc3545; font-size: 0.9rem;"></i>
                                    </button>
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
                { 
                    targets: 2,
                    type: 'date-eu'
                },
                {
                    targets: 7,
                    type: 'string',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data;
                        }
                        return data.includes('Commande Traitée') ? 'Traitée' : 'Non traité';
                    }
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[2, 'asc']],
            pageLength: 10, // nombre max de ligne sur la pagination
        });
    
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
