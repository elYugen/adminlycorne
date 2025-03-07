@extends('layouts.base')
@section('title', 'Gestion des utilisateurs')
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
</style>
@endsection

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Gestion des utilisateurs</h1>

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
        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#createUser" style="background-color: #205558; color: white;">
            <i class="bi bi-person-plus"></i> Ajouter un utilisateur
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover user-table">
            <thead class="table">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}" title="Modifier l'utilisateur">
                            <i class="bi bi-pencil"></i> 
                        </button>
                        @if(Auth::id() !== $user->id)
                            <button type="button" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUser{{ $user->id }}" title="Supprimer l'utilisateur">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('.user-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: [3] }
            ]
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        // verif si mots de passe identique
        $('#createUser form').on('submit', function(e) {
            const password = $('#password').val();
            const confirmation = $('#password_confirmation').val();
            
            if (password !== confirmation) {
                e.preventDefault();
                $('#password-error').show();
                return false;
            }
            $('#password-error').hide();
            return true;
        });
    });
</script>
@endsection
@include('layouts.components.user_modal')