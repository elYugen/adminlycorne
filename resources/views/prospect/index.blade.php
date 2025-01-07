@extends('layouts.base')
@section('title', 'Gestion des entreprises')
    
@section('content')
    @include('layouts.components.navbar')

    <div class="container">
        <h1 class="mt-4">Gestion des entreprises</h1>

        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
        @endif

    <div class="mt-4 mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUser">
            <i class="bi bi-person-plus"></i> Ajouter une entreprise
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
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
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->gender === 'homme' ? 'Mr' : 'Mme' }}. {{ $user->firstname }} {{ $user->lastname }} </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->address }}, {{ $user->postal_code }}</td>
                    <td>{{ $user->phone_number }}</td>
                    <td>{{ $user->company }}</td>
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
<div class="d-flex justify-content-center">
    {{ $users->links() }}
</div>
    </div>
@endsection

@include('layouts.components.prospect_modal')