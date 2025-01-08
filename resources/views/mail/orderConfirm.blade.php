@extends('layouts.base')
@section('title', 'Confirmation de la commande')
@section('content')
<div class="container">
<form action="{{ route('orders.confirm', $commande->id) }}" method="post">
    @csrf
    <br>
    <h1>Condition général de vente</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. <br>Libero odit et voluptatum odio adipisci aperiam sapiente dolores id deserunt velit mollitia quisquam, <br>magni earum culpa ipsa? Iusto aperiam necessitatibus repudiandae.</p>
    <br>
    <div class="form-check">
        <input type="checkbox" name="is_cgv_validated" id="is_cgv_validated" class="form-check-input" value="1" required>
        <label for="is_cgv_validated" class="form-check-label">Accepter les CGV</label>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Confirmer la commande</button>
</form>
</div>
@endsection