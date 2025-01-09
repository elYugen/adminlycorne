@extends('layouts.base')
@section('title', 'Confirmation de la commande')
@section('content')
<div class="container">
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
        
    <br>
    <h1>Condition général de vente</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. <br>Libero odit et voluptatum odio adipisci aperiam sapiente dolores id deserunt velit mollitia quisquam, <br>magni earum culpa ipsa? Iusto aperiam necessitatibus repudiandae.</p>
    <br>

    <form action="{{ route('orders.confirm', $commande->id) }}" method="post">
    @csrf
    <div class="mb-3">
        <label for="modalite_paiement" class="form-label">Modalité de paiement</label>
        <select name="modalite_paiement" id="modalite_paiement" class="form-select" required>
            <option value="">Sélectionnez un moyen de paiement</option>
            <option value="prelevement">Prélèvement</option>
            <option value="virement">Carte Bancaire</option>
            <option value="cheque">Chèque</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="planification" class="form-label">Planification de paiement</label>
        <select name="planification" id="planification" class="form-select" required>
            <option value="">Sélectionnez une planification</option>
            <option value="annuel">Annuel</option>
            <option value="trimestriel">Trimestriel</option>
            <option value="semestriel">Semestriel</option>
            <option value="mensuel">Mensuel</option>
        </select>
    </div>

    <div id="iban-bic-container" style="display: none;">
        <div class="mb-3">
            <label for="iban" class="form-label">IBAN</label>
            <input type="text" name="iban" id="iban" class="form-control" maxlength="34" placeholder="FR76 3000 3000 ..." />
        </div>
        <div class="mb-3">
            <label for="bic" class="form-label">BIC</label>
            <input type="text" name="bic" id="bic" class="form-control" maxlength="11" placeholder="ABCDEFGHIJK" />
        </div>
        <div class="form-check">
            <input type="checkbox" name="authorization" id="authorization" class="form-check-input" value="1" required>
            <label for="authorization" class="form-check-label">J'autorise L au carré à prélever les paiements de mes factures sur le compte bancaire ci-dessus</label>
        </div>
    </div>

    <div class="form-check mt-4">
        <input type="checkbox" name="is_cgv_validated" id="is_cgv_validated" class="form-check-input" value="1" required>
        <label for="is_cgv_validated" class="form-check-label">Accepter les CGV</label>
    </div>
    
    <button type="submit" class="btn btn-primary">Confirmer la commande</button>
</form>

    <!-- Informations légales de l'entreprise -->
    <div class="mt-5">
        <h2>Informations légales</h2>
        <p>
            <strong>L AU CARRE</strong> - SAS au capital de 2 506 € <br>
            Granoux, 15 700 PLEAUX <br>
            Siret : 804 485 167 000 22 <br>
            APE : 8299Z - RCS AURILLAC B 804 485 167
        </p>
    </div>
</div>

<script>
    document.getElementById('modalite_paiement').addEventListener('change', function() {
        const value = this.value;
        const ibanBicContainer = document.getElementById('iban-bic-container');

        if (value === 'prelevement') {
            ibanBicContainer.style.display = 'block';
        } else {
            ibanBicContainer.style.display = 'none';
        }
    });
</script>
@endsection