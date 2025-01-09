@extends('layouts.base')
@section('title', 'Connexion')
@section('styles')
<style>
main {
    height: 100vh;
}

.form-signin {
  max-width: 500px;
  padding: 1rem;
}

.form-signin .form-floating:focus-within {
  z-index: 2;
}

.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}

.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
    </style>
@endsection

@section('content')
<main class="form-signin w-100 m-auto d-flex align-items-center justify-content-center">
    <form action="{{ route('auth.authenticate') }}" method="POST">
        @csrf
      <h1 class="h3 mb-3 fw-normal">Se connecter</h1>
  
      <div class="form-floating">
        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email">
        <label for="floatingInput">Adresse mail</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control" id="floatingPassword" placeholder="Mot de passe" name="password">
        <label for="floatingPassword">Mot de passe</label>
      </div>
  
      <div class="form-check text-start my-3">
        <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          Se souvenir de moi
        </label>
      </div>
      <button class="btn w-100 py-2" type="submit" style="background-color: #362258; color: white;">Connexion</button>
      <p class="mt-5 mb-3 text-body-secondary">&copy; 2024</p>
    </form>
  </main>
@endsection