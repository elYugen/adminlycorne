@extends('layouts.base')
@section('title', 'Gestion utilisateur')
    
@section('content')
    @include('layouts.components.navbar')

    <div class="container">
        <h1 class="mt-4">Gestion des utilisateurs</h1>

        <!-- Affiche un message de succès -->
        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif

            <!-- Bouton pour créer un utilisateur -->
    <div class="mt-4 mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUser">
            <i class="bi bi-person-plus"></i> Créer un utilisateur
        </button>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Prénom Nom</th>
                    <th>Email</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Entreprise</th>
                    <th>Siret</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }} {{ $user->fullname }} </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->address }}, {{ $user->postal_code }}</td>
                    <td>{{ $user->phone_number }}</td>
                    <td>{{ $user->entreprise }}</td>
                    <td>{{ $user->siret }}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}">
                            <i class="bi bi-pencil"></i> Modifier
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUser{{ $user->id }}">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

    </div>
@endsection

@include('layouts.components.users_modal')