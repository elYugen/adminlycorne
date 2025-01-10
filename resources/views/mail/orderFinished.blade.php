@extends('layouts.base')
@section('title', 'Confirmation de la commande')
@section('styles')
    <style>
        .logo {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-content: center;
        }
        .logo img {
            width: 400px;
        }
        hr {
            width: 100%;
            border: none;
            background-color: #b7b7c5;
            height: 2px;
            margin: 10px 0;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="logo">
        <img src="{{ asset('sd_laucarre.png') }}" alt="Logo">
        <hr />
    </div>
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

@endsection