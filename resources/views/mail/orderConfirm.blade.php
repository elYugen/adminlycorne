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
        
    <br>
    <h1>Confirmation de commande {{ $commande->numero_commande }}</h1>
    <br>

    <form action="{{ route('orders.confirm', $commande->id) }}" method="post">
    @csrf

    <div id="validationErrors" class="alert alert-danger" style="display: none;">
        <ul id="errorList"></ul>
    </div>

    <label for="planification" class="form-label">Planification de paiement</label>
    <select name="planification" id="planification" class="form-select" required>
        <option value="">Sélectionnez une planification</option>
        <option value="annuel">Annuel</option>
        <option value="trimestriel">Trimestriel</option>
        <option value="semestriel">Semestriel</option>
        <option value="mensuel">Mensuel</option>
    </select>

<br>
    <div class="mb-3">
        <label for="modalite_paiement" class="form-label">Modalité de paiement</label>
        <select name="modalite_paiement" id="modalite_paiement" class="form-select" required>
            <option value="">Sélectionnez un moyen de paiement</option>
            <option value="prelevement">Prélèvement</option>
            <option value="virement">Carte Bancaire</option>
            <option value="cheque">Chèque</option>
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
            <input type="checkbox" name="authorization" id="authorization" class="form-check-input" value="1">
            <label for="authorization" class="form-check-label">J'autorise L au carré à prélever les paiements de mes factures sur le compte bancaire ci-dessus</label>
        </div>
    </div>

    <div class="form-check mt-4">
        <input type="checkbox" name="is_cgv_validated" id="is_cgv_validated" class="form-check-input" value="1" required>
        <label for="is_cgv_validated" class="form-check-label">Accepter les CGV</label>
    </div>
    
    <!-- bouton de confirmation apparait pour chèque et prélèvement -->
    <button type="submit" id="confirm-button" class="btn mt-1" style="background-color: #362258; color: white; display: none;">
        Confirmer la commande
    </button>
    
    <!-- bouton Stripe apparait pour carte bancaire -->
    <div id="stripe-container" style="display: none;" class="mt-1 mb-1">
        <button type="button" id="stripe-button" class="btn mt-1" style="background-color: #362258; color: white;">
            Payer avec Stripe
        </button>
    </div>
</form>

<div class="mt-5">
    <hr>
        <h3>Informations légales</h3>
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
    const authorizationCheckbox = document.getElementById('authorization');
    const stripeContainer = document.getElementById('stripe-container');
    const confirmButton = document.getElementById('confirm-button');

    // reset l'affichage des boutons
    stripeContainer.style.display = 'none';
    confirmButton.style.display = 'none';

    if (value === 'prelevement') {
        ibanBicContainer.style.display = 'block';
        authorizationCheckbox.setAttribute('required', 'required');
        confirmButton.style.display = 'block';
    } else if (value === 'virement') { // carte bancaire
        ibanBicContainer.style.display = 'none';
        stripeContainer.style.display = 'block';
        authorizationCheckbox.removeAttribute('required');
    } else if (value === 'cheque') {
        ibanBicContainer.style.display = 'none';
        authorizationCheckbox.removeAttribute('required');
        confirmButton.style.display = 'block';
    } else {
        // si aucun moyen de paiement est choisis
        ibanBicContainer.style.display = 'none';
        authorizationCheckbox.removeAttribute('required');
    }
});

document.getElementById('stripe-button').addEventListener('click', function(e) {
    e.preventDefault();
    
    // reset les anciennes erreur
    const errorContainer = document.getElementById('validationErrors');
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';
    errorContainer.style.display = 'none';
    
    // verifie les champs requis
    const planification = document.getElementById('planification').value;
    const cgvChecked = document.getElementById('is_cgv_validated').checked;
    
    let errors = [];
    
    if (!planification) {
        errors.push("veuillez sélectionner une planification de paiement");
    }
    
    if (!cgvChecked) {
        errors.push("veuillez accepter les conditions générales de vente");
    }
    
    if (errors.length > 0) {
        // afficher les erreurs dans l'alerte Bootstrap
        errorContainer.style.display = 'block';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        
        // Scroll vers les erreurs
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    // si tout est valide on accède au paiement Stripe
    fetch("{{ route('stripe.checkout.session') }}", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            commande_id: {{ $commande->id }},
            amount: {{ $commande->total_ttc * 100 }},
            planification: planification,
            is_cgv_validated: true
        }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("erreur Stripe: " + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        stripe.redirectToCheckout({ sessionId: data.id });
    })
    .catch(error => {
        console.error('erreur Stripe:', error);
        errorContainer.style.display = 'block';
        const li = document.createElement('li');
        li.textContent = "une erreur est survenue lors de la création de la session stripe";
        errorList.appendChild(li);
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
});
</script>
@endsection

@section('script')
<script src="https://js.stripe.com/v3/"></script>
@endsection