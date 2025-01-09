@extends('layouts.base')
@section('title', 'Gestion des utilisateurs')
    
@section('content')
    @include('layouts.components.navbar')

    <div class="container">
        <h1 class="mt-4">Gestion des utilisateurs</h1>

        <!-- Affiche un message de succès et d'erreur-->
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
        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#createUser" style="background-color: #b7b7c5; color: white;">
            <i class="bi bi-person-plus"></i> Ajouter un utilisateur
        </button>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
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

@include('layouts.components.user_modal')