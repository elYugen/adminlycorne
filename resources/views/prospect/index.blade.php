@extends('layouts.base')
@section('title', 'Gestion des prospects')
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
    @include('layouts.components.navbar')

    <div class="container">
        <h1 class="mt-4">Gestion des prospects</h1>

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
        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#createprospect" style="background-color: #b7b7c5; color: white;">
            <i class="bi bi-person-plus"></i> Ajouter un prospect
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover prospect-table">
            <thead class="table">
                <tr>
                    <th>#</th>
                    <th>Nom Prénom</th>
                    <th>Email</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Entreprise</th>
                    <th>Siret</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prospects as $prospect)
                <tr>
                    <td>{{ $prospect->id }}</td>
                    <td>{{ $prospect->firstname }} {{ $prospect->lastname }} </td>
                    <td>{{ $prospect->email }}</td>
                    <td>{{ $prospect->address }}, {{ $prospect->city }} {{ $prospect->postal_code }}</td>
                    <td>{{ $prospect->phone_number }}</td>
                    <td>{{ $prospect->company }}</td>
                    <td>{{ $prospect->siret }}</td>
                    <td>
                        <div class="d-flex gap-1 h-100 align-items-center">
                            <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editprospect{{ $prospect->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#deleteprospect{{ $prospect->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
    </div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('.prospect-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [7] } 
            ],
            pageLength: 10,
        });
    });
</script>
@endsection

@include('layouts.components.prospect_modal')