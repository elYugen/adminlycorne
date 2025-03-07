@extends('layouts.base')
@section('title', "Création d'une commande")

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .prospect-search-wrapper {
        margin: 0 auto 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .prospect-search-container {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    #searchResults {
        border: 1px solid #ddd;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: absolute;
        width: 100%;
        z-index: 1000;
    }
    #searchResults button {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 10px;
        border: none;
        background: none;
        text-align: left;
        font-size: 14px;
        cursor: pointer;
    }
    #searchResults button:hover {
        background: #f8f9fa;
    }
    #searchResults button .prospect-info {
        margin-left: 10px;
    }
    #searchResults button .prospect-name {
        font-weight: bold;
    }
    #searchResults button .prospect-company {
        color: #6c757d;
        font-size: 0.9em;
    }
    .prospect-search-box {
        position: relative;
        flex-grow: 1;
    }
    #prospectCard {
        max-width: 400px;
    }
    #prospectCard .card-body {
        padding: 15px;
    }
    #prospectCard .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    #prospectCard .card-text {
        font-size: 0.9rem;
    }
    
    .produit-row {
        margin: 0 auto 20px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        background: #fff;
    }

    .produit-row .form-group {
        margin-bottom: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .produit-row .btn-danger {
        margin-top: 32px; 
    }

    .produit-row label {
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .produit-row .select-label {
        margin-bottom: 8px;
        visibility: hidden;
    }

    .produit-row .col-md-2, .produit-row .col-md-3, .produit-row .col-md-4 {
        margin-bottom: 10px;
    }

    .total-section {
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
    }
    
</style>
@endsection

@section('content')
@include('layouts.components.navbar')

<div class="container">
    <h1 class="mt-4">Création d'une commande</h1>

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

    <form id="commandeForm" action="{{ route('orders.add') }}" method="POST">
        @csrf
        <div class="prospect-search-wrapper">
            <h6 class="mb-4">Prospect</h6>
            <div class="prospect-search-container">
                <div class="prospect-search-box flex-grow-1">
                    <input type="text" id="searchProspect" class="form-control" placeholder="Rechercher un prospect...">
                    <div id="searchResults" class="mt-2"></div>
                </div>

                <button type="button" class="btn btn-sm" style="background-color: #133a3f; color: white" data-bs-toggle="modal" data-bs-target="#addProspectModal">
                    Créer un nouveau prospect
                </button>
            </div>
            
            <!-- carte du prospect -->
            <div id="prospectCard" style="display: none;" class="card">
                <div class="card-body">
                    <div id="prospectSuccess" class="alert alert-success mb-3" style="display: none;"></div>
                    <h5 id="prospectName" class="card-title">
                        <i class="bi bi-person-circle"></i>
                        <span id="prospectNameText"></span>
                        <span id="prospectCompanyText"></span>
                    </h5>
                    <div class="card-text">
                        <p id="prospectDetails"></p>
                        <p id="prospectAddress"></p>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="client_id" id="client_id" required>

            <h6 class="mb-4">Produits</h6>
            <div id="produitsList">
                @if(old('produits'))
                    @foreach(old('produits') as $index => $oldProduit)
                    <div class="row mb-2 produit-row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="select-label">Produit</label> 
                                <select name="produits[{{ $index }}][produit_id]" class="form-select produit-select" required>
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_ht }}" 
                                        {{ $oldProduit['produit_id'] == $produit->id ? 'selected' : '' }}>
                                        {{ $produit->nom }} - {{ $produit->prix_ht }} €
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" name="produits[{{ $index }}][quantite]" 
                                    class="form-control quantite-input" 
                                    value="{{ $oldProduit['quantite'] }}" min="1" pattern="[0-9\s,.]+" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix unitaire HT</label>
                                <input type="number" step="0.01" name="produits[{{ $index }}][prix_ht]" 
                                    class="form-control prix-ht-input"  
                                    value="{{ $oldProduit['prix_ht'] }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix unitaire TTC</label>
                                <input type="number" step="0.01" name="produits[{{ $index }}][prix_ttc]" 
                                    class="form-control prix-ttc-input" readonly  style="background-color: #e9ecef;">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product" {{ $index === 0 ? 'style=display:none' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="row mb-2 produit-row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="select-label">Produit</label> 
                                <select name="produits[0][produit_id]" class="form-select produit-select" required>
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_ht }}">
                                        {{ $produit->nom }} - {{ $produit->prix_ht }} €
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Quantité</label>
                                <input type="number" name="produits[0][quantite]" class="form-control quantite-input" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix unitaire HT</label>
                                <input type="number" step="0.01" name="produits[0][prix_ht]" class="form-control prix-ht-input">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Prix unitaire TTC</label>
                                <input type="number" step="0.01" name="produits[0][prix_ttc]" class="form-control prix-ttc-input" readonly style="background-color: #e9ecef;">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-secondary mt-2" id="addProductRow">Ajouter un produit</button>

        <div class="total-section">
            <div class="row">
                <div class="col-md-6">
                    <strong>Total HT:</strong> <span id="totalHT">0.00</span> €
                </div>
                <div class="col-md-6">
                    <strong>Total TTC:</strong> <span id="totalTTC">0.00</span> €
                </div>
            </div>
        </div>

        <button type="submit" class="btn mt-4" style="color: white; background-color:#133a3f;">Créer la commande</button>
    </form>
</div>

<div class="modal fade" id="addProspectModal" tabindex="-1" aria-labelledby="addProspectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addProspectForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProspectModalLabel">Ajouter un Prospect</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">Prénom</label>
                            <input type="text" id="firstname" name="firstname" placeholder="Prénom" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Nom</label>
                            <input type="text" id="lastname" name="lastname" placeholder="Nom" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" placeholder="Email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Civilité</label>
                            <input type="text" id="gender" name="gender" placeholder="Civilité" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Numéro de téléphone *</label>
                            <input type="text" id="phone_number" name="phone_number" placeholder="Numéro de téléphone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Adresse *</label>
                            <input type="text" id="address" name="address" placeholder="Adresse" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="city" class="form-label">Ville *</label>
                            <input type="text" id="city" name="city" placeholder="Ville" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">Code postal *</label>
                            <input type="text" id="postal_code" name="postal_code" placeholder="Code postal" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="company" class="form-label">Entreprise</label>
                            <input type="text" id="company" name="company" placeholder="Entreprise" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="siret" class="form-label">SIRET</label>
                            <input type="text" id="siret" name="siret" placeholder="SIRET" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('addProspectForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('{{ route('prospect.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json' 
            }
        });

        if (response.ok) {
            const data = await response.json();

            const successDiv = document.getElementById('prospectSuccess');
            successDiv.textContent = data.success;
            successDiv.style.display = 'block';

            // maj de la card avec les nouvelles info
            const firstName = data.prospect.firstname ? data.prospect.firstname : 'Non renseigné';
            const lastName = data.prospect.lastname ? data.prospect.lastname : 'Non renseigné';
            const company = data.prospect.company ? data.prospect.company : 'Non renseigné';

            document.getElementById('prospectName').textContent = `${firstName} ${lastName}`;
            document.getElementById('prospectDetails').textContent = 
                `Email: ${data.prospect.email}, Téléphone: ${data.prospect.phone_number}, Entreprise: ${company}`;
            document.getElementById('prospectCard').style.display = 'block';

            // ajouter l'id du prospect dans un input caché
            document.getElementById('client_id').value = data.prospect.id;

            const modal = bootstrap.Modal.getInstance(document.getElementById('addProspectModal'));
            modal.hide();
        } else {
            const error = await response.json();
            alert(error.error || 'Erreur lors de l\'ajout');
        }
    } catch (error) {
        console.error(error);
        alert('Erreur réseau');
    }
});

document.getElementById('searchProspect').addEventListener('input', async function () {
    const query = this.value.trim();

    if (!query) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }

    const response = await fetch(`/prospect/search?query=${query}`);
    const prospects = await response.json();

    const resultsDiv = document.getElementById('searchResults');
    resultsDiv.innerHTML = '';

    prospects.forEach(prospect => {
        const button = document.createElement('button');
        button.classList.add('btn');

        // constante pour l'affichage des infos
        const firstName = prospect.firstname || 'Prénom non renseigné';
        const lastName = prospect.lastname || 'Nom non renseigné';
        const company = prospect.company || 'Entreprise non renseigné';

        // contenu de la recherche
        button.innerHTML = `
            <i class="bi bi-person-circle"></i>
            <div class="prospect-info">
                <div class="prospect-name">${firstName} ${lastName}</div>
                <div class="prospect-company">${company}</div>
            </div>`;
        
        button.addEventListener('click', () => {
            // set le nom et l'entreprise du prospect
            const displayName = firstName || lastName ? `${firstName} ${lastName}`.trim() : company;
            
            document.getElementById('prospectNameText').textContent = displayName;
            
            // set les infos du prospect
            document.getElementById('prospectDetails').innerHTML = `
                <strong>Téléphone:</strong> ${prospect.phone_number}<br>
                <strong>Email:</strong> ${prospect.email}`;
            
            // set l'adresse du prospect
            document.getElementById('prospectAddress').innerHTML = `
                <strong>Adresse:</strong> ${prospect.address}, ${prospect.postal_code} ${prospect.city}`;
            
            document.getElementById('prospectCard').style.display = 'block';
            document.getElementById('client_id').value = prospect.id;
            
            // vide la recherche
            resultsDiv.innerHTML = '';
            document.getElementById('searchProspect').value = '';
        });
        
        resultsDiv.appendChild(button);
    });
});


let productIndex = {{ old('produits') ? count(old('produits')) : 1 }};

// ajouter un produit
document.getElementById('addProductRow').addEventListener('click', function () {
    const productRow = `
        <div class="row mb-2 produit-row align-items-end">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="select-label">Produit</label>
                    <select name="produits[${productIndex}][produit_id]" class="form-select produit-select" required>
                        <option value="">Sélectionnez un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_ht }}">
                                {{ $produit->nom }} - {{ $produit->prix_ht }} €
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" name="produits[${productIndex}][quantite]" class="form-control quantite-input" value="1" min="1" required>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Prix unitaire HT</label>
                    <input type="number" step="0.01" name="produits[${productIndex}][prix_ht]" class="form-control prix-ht-input">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Prix unitaire TTC</label>
                    <input type="number" step="0.01" name="produits[${productIndex}][prix_ttc]" class="form-control prix-ttc-input" readonly style="background-color: #e9ecef;">
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-product">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    document.getElementById('produitsList').insertAdjacentHTML('beforeend', productRow);
    productIndex++;
    updateTotals();
});

// met a jour le total
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('prix-ht-input')) {
        const row = e.target.closest('.produit-row');
        const prixTtcInput = row.querySelector('.prix-ttc-input');
        const prixHT = parseFloat(e.target.value) || 0;
        prixTtcInput.value = (prixHT * 1.2).toFixed(2);
        updateTotals();
    }
});


// suppr un produit
document.addEventListener('click', function (e) {
    if (e.target.closest('.remove-product')) {
        const row = e.target.closest('.produit-row');
        if (document.querySelectorAll('.produit-row').length > 1) {
            row.remove();
            updateTotals();
        }
    }
});

// recalculer les totaux
function updateTotals() {
    let totalHT = 0;
    let totalTTC = 0;
    const taxRate = 1.2; // TVA à 20%

    document.querySelectorAll('.produit-row').forEach(row => {
        const produitSelect = row.querySelector('.produit-select');
        const prixHtInput = row.querySelector('.prix-ht-input');
        const prixTtcInput = row.querySelector('.prix-ttc-input');
        const quantiteInput = row.querySelector('.quantite-input');
        
        // Si un produit est sélectionné et que le prix HT est vide on utilise le prix par défaut
        if (produitSelect.selectedOptions[0]?.dataset?.prix && !prixHtInput.value) {
            const defaultPrix = parseFloat(produitSelect.selectedOptions[0].dataset.prix);
            prixHtInput.value = defaultPrix.toFixed(2);
            prixTtcInput.value = (defaultPrix * 1.2).toFixed(2);
        }
        
        const prixUnitaireHT = parseFloat(prixHtInput.value) || 0;
        const quantite = parseFloat(quantiteInput.value) || 1;
        
        // Calcul des prix
        const prixTotalHT = prixUnitaireHT * quantite;
        const prixTotalTTC = prixTotalHT * taxRate;
        
        // Mise à jour du prix TTC uniquement si le prix HT a changé
        if (prixHtInput.value) {
            prixTtcInput.value = (prixUnitaireHT * taxRate).toFixed(2);
        }
        
        totalHT += prixTotalHT;
        totalTTC += prixTotalTTC;
    });

    document.getElementById('totalHT').textContent = totalHT.toFixed(2);
    document.getElementById('totalTTC').textContent = totalTTC.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('produit-select')) {
        const row = e.target.closest('.produit-row');
        const prixHtInput = row.querySelector('.prix-ht-input');
        const prixTtcInput = row.querySelector('.prix-ttc-input');
        const quantiteInput = row.querySelector('.quantite-input');
        const selectedOption = e.target.selectedOptions[0];
        
        quantiteInput.value = '1';
        
        if (selectedOption?.dataset?.prix) {
            const prixHT = parseFloat(selectedOption.dataset.prix);
            prixHtInput.value = prixHT.toFixed(2);
            prixTtcInput.value = (prixHT * 1.2).toFixed(2);
            updateTotals();
        } else {
            prixHtInput.value = '';
            prixTtcInput.value = '';
        }
    }
});

// formater un nombre en format fr
function formatNumberFr(number) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
        useGrouping: true
    }).format(number);
}

// parser un nombre au format fr
function parseNumberFr(str) {
    if (typeof str !== 'string') return str;
    
    // supprime les espaces
    str = str.replace(/\s/g, '');
    // remplace la virgule par un point
    str = str.replace(',', '.');
    // supprime les zéros non significatifs au début
    str = str.replace(/^0+([0-9])/g, '$1');
    
    const parsed = parseFloat(str);
    return isNaN(parsed) ? 0 : parsed;
}

document.addEventListener('blur', function(e) {
    if (e.target.classList.contains('quantite-input')) {
        const value = parseNumberFr(e.target.value);
        e.target.value = formatNumberFr(value);
        updateTotals();
    }
}, true);
</script>
@endsection