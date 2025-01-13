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
            height: auto;
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
        <img src="{{ e(asset('sd_laucarre.png')) }}" alt="Logo">
        <hr />
    </div>
        <!-- gestion de succès et erreur -->
        @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ e(session('success')) }}
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ e($error) }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
    <br>
    <h1>Confirmation de commande {{ e($commande->numero_commande) }}</h1>
    <br>

    <form action="{{ route('orders.confirm', $commande->id) }}" method="post" data-token="{{ $token }}">
    @csrf
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div id="validationErrors" class="alert alert-danger" style="display: none;">
        <ul id="errorList"></ul>
    </div>

    <div class="mb-3">
        <label for="modalite_paiement" class="form-label">Modalité de paiement</label>
        <select name="modalite_paiement" id="modalite_paiement" class="form-select" required data-original="{{ old('modalite_paiement') }}">
            <option value="">Sélectionnez un moyen de paiement</option>
            <option value="prelevement">Prélèvement</option>
            <option value="virement">Carte Bancaire</option>
            <option value="cheque">Chèque</option>
        </select>
    </div>

    <div id="planification-container" style="display: none;" class="mb-3">
        <label for="planification" class="form-label">Planification de paiement</label>
        <select name="planification" id="planification" class="form-select" data-original="{{ old('planification') }}">
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
            <input type="text" name="iban" id="iban" class="form-control" maxlength="34" pattern="[A-Z0-9\s]{14,34}" placeholder="FR76 3000 3000 ..." />
        </div>
        <div class="mb-3">
            <label for="bic" class="form-label">BIC</label>
            <input type="text" name="bic" id="bic" class="form-control" maxlength="11" pattern="[A-Z0-9]{8,11}" placeholder="ABCDEFGHIJK" />
        </div>
        <div class="form-check">
            <input type="checkbox" name="authorization" id="authorization" class="form-check-input" value="1">
            <label for="authorization" class="form-check-label">J'autorise L au carré à prélever les paiements de mes factures sur le compte bancaire ci-dessus</label>
        </div>
    </div>

    <div class="form-check mt-4">
        <input type="checkbox" name="is_cgv_validated" id="is_cgv_validated" class="form-check-input" value="1" required>
        <label for="is_cgv_validated" class="form-check-label">J'ai lu et j'accepte les <a href="{{ e(asset('CGV.pdf')) }}" target="_blank" rel="noopener noreferrer">conditions générales de ventes</a></label>
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
const urlParams = new URLSearchParams(window.location.search);
const paymentToken = urlParams.get('token');
const VALID_PAYMENT_METHODS = ['prelevement', 'virement', 'cheque'];
const VALID_PLANIFICATIONS = ['annuel', 'trimestriel', 'semestriel', 'mensuel'];
//const IBAN_REGEX = ^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$;
//const BIC_REGEX = ^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$;

// fonction de nettoyage des entrées
function sanitizeInput(input) {
    return input.replace(/[<>]/g, '').trim();
}

// fonction de validation de l'iban
function validateIBAN(iban) {
    iban = iban.replace(/\s/g, '').toUpperCase();
    return IBAN_REGEX.test(iban);
}

// fonction de validation du bic
function validateBIC(bic) {
    bic = bic.toUpperCase();
    return BIC_REGEX.test(bic);
}

// gestion du changement de mode de paiement
document.getElementById('modalite_paiement').addEventListener('change', function() {
    const value = sanitizeInput(this.value);
    
    if (!VALID_PAYMENT_METHODS.includes(value)) {
        this.value = '';
        return;
    }

    const planificationContainer = document.getElementById('planification-container');
    const planificationSelect = document.getElementById('planification');
    const ibanBicContainer = document.getElementById('iban-bic-container');
    const authorizationCheckbox = document.getElementById('authorization');
    const stripeContainer = document.getElementById('stripe-container');
    const confirmButton = document.getElementById('confirm-button');

    // reset l'affichage des boutons et conteneurs
    [stripeContainer, confirmButton, ibanBicContainer].forEach(el => {
        if (el) el.style.display = 'none';
    });

    // reset les champs iban et bic si on change de mode de paiement
    if (value !== 'prelevement') {
        const ibanInput = document.getElementById('iban');
        const bicInput = document.getElementById('bic');
        if (ibanInput) ibanInput.value = '';
        if (bicInput) bicInput.value = '';
        planificationSelect.value = '';
        planificationContainer.style.display = 'none';
    } else {
        planificationContainer.style.display = 'block';
    }

    switch(value) {
        case 'prelevement':
            ibanBicContainer.style.display = 'block';
            confirmButton.style.display = 'block';
            if (authorizationCheckbox) {
                authorizationCheckbox.setAttribute('required', 'required');
            }
            break;
        case 'virement':
            stripeContainer.style.display = 'block';
            if (authorizationCheckbox) {
                authorizationCheckbox.removeAttribute('required');
            }
            break;
        case 'cheque':
            confirmButton.style.display = 'block';
            if (authorizationCheckbox) {
                authorizationCheckbox.removeAttribute('required');
            }
            break;
    }
});

// validation des champs iban et bic
if (document.getElementById('iban')) {
    document.getElementById('iban').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^A-Z0-9\s]/gi, '').toUpperCase();
    });
}

if (document.getElementById('bic')) {
    document.getElementById('bic').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase();
    });
}

// gestion du bouton Stripe
document.getElementById('stripe-button').addEventListener('click', function(e) {
    e.preventDefault();
    
    const errorContainer = document.getElementById('validationErrors');
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';
    errorContainer.style.display = 'none';
    
    const selectedPaymentMethod = document.getElementById('modalite_paiement').value;
    const cgvChecked = document.getElementById('is_cgv_validated').checked;
    
    let errors = [];
    let planificationValue = null;
    
    if (selectedPaymentMethod === 'prelevement') {
        planificationValue = sanitizeInput(document.getElementById('planification').value);
        if (!planificationValue || !VALID_PLANIFICATIONS.includes(planificationValue)) {
            errors.push("veuillez sélectionner une planification de paiement valide");
        }
    }
    
    if (!cgvChecked) {
        errors.push("veuillez accepter les conditions générales de vente");
    }
    
    if (errors.length > 0) {
        errorContainer.style.display = 'block';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    // double vérification du token csrf
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // appel à stripe avec retry limité
    let retryCount = 0;
    const maxRetries = 3;

    function processStripePayment() {
        fetch("{{ route('stripe.checkout.session') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                commande_id: {{ $commande->id }},
                amount: {{ $commande->total_ttc * 100 }},
                planification: planificationValue,
                is_cgv_validated: true,
                token: urlParams.get('token')
            }),
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.error || `erreur stripe: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (!data.id) {
                throw new Error('id session manquant');
            }
            const stripe = Stripe("{{ env('STRIPE_KEY') }}");
            return stripe.redirectToCheckout({ sessionId: data.id });
        })
        .catch(error => {
            console.error('erreur stripe:', error);
            retryCount++;
            
            if (retryCount < maxRetries) {
                setTimeout(processStripePayment, 1000 * retryCount);
            } else {
                errorContainer.style.display = 'block';
                const li = document.createElement('li');
                li.textContent = "Une erreur est survenue lors de la création de la session de paiement.";
                errorList.appendChild(li);
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    processStripePayment();
});

// protection contre la double soumission
const confirmationForm = document.getElementById('confirmation-form');
if (confirmationForm) {
    confirmationForm.addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            setTimeout(() => {
                submitButton.disabled = false;
            }, 5000);
        }
    });
}
</script>
@endsection

@section('script')
<script src="https://js.stripe.com/v3/"></script>
@endsection