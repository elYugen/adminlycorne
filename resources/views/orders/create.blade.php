@extends('layouts.base')
@section('title', "Création d'une commande")

@section('styles')
<style>
    #searchResults {
        border: 1px solid #ddd;
        border-radius: 5px;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    #searchResults button {
        display: block;
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
    .produit-row {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        background: #f8f9fa;
    }

    .produit-row {
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    background: #f8f9fa;
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
    }

    .produit-row .select-label {
        margin-bottom: 8px;
        visibility: hidden;
    }

    .produit-row .col-md-2, .produit-row .col-md-3, .produit-row .col-md-4 {
        margin-bottom: 10px;
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

    <form id="commandeForm" action="{{ route('orders.add') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" id="searchProspect" class="form-control" placeholder="Rechercher un prospect...">
            <div id="searchResults" class="mt-2"></div>
        </div>
        
        <!-- Card -->
        <div id="prospectCard" style="display: none;" class="card">
            <div class="card-body">
                <h5 id="prospectName" class="card-title"></h5>
                <p id="prospectDetails" class="card-text"></p>
            </div>
        </div>
<br>
        <input type="hidden" name="client_id" id="client_id" required>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProspectModal">
            Ajouter un Prospect
        </button>

        <div id="produitsList" class="mt-4">
            <h6>Produits</h6>
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantité</label>
                        <input type="number" name="produits[0][quantite]" class="form-control quantite-input" value="1" min="1" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Prix unitaire HT</label>
                        <input type="number" step="0.01" name="produits[0][prix_ht]" class="form-control prix-input" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-product">Supprimer</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addProductRow">Ajouter un produit</button>

        <div class="mt-3">
            <strong>Total HT:</strong> <span id="totalHT">0.00</span> €
            <br>
            <strong>Total TTC:</strong> <span id="totalTTC">0.00</span> €
        </div>

        <button type="submit" class="btn btn-primary mt-4">Créer la commande</button>
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
                    <input type="text" class="form-control mb-3" name="firstname" placeholder="Prénom">
                    <input type="text" class="form-control mb-3" name="lastname" placeholder="Nom">
                    <input type="email" class="form-control mb-3" name="email" placeholder="Email" required>
                    <input type="text" class="form-control mb-3" name="phone_number" placeholder="Numéro de téléphone" required>
                    <input type="text" class="form-control mb-3" name="company" placeholder="Entreprise">
                    <input type="text" class="form-control mb-3" name="siret" placeholder="SIRET">
                    <input type="text" class="form-control mb-3" name="address" placeholder="Adresse" required>
                    <input type="text" class="form-control mb-3" name="postal_code" placeholder="Code postal" required>
                    <input type="text" class="form-control mb-3" name="city" placeholder="Ville" required>
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
            alert(data.success);

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
    
    // vérification du nom, prénom et entreprise
    const firstName = prospect.firstname ? prospect.firstname : '';
    const lastName = prospect.lastname ? prospect.lastname : '';
    const company = prospect.company ? prospect.company : '';
    
    // décide quoi afficher comme texte du bouton
    let displayText;
    if (firstName || lastName) {
        // si on a un nom ou prénom alors on affiche en premier
        displayText = `${firstName} ${lastName}`;
        if (company) {
            displayText += ` - ${company}`;
        }
    } else if (company) {
        // si on a pas de nom/prénom mais une entreprise
        displayText = company;
    } else {
        // si on a ni nom/prénom ni entreprise
        displayText = 'Contact sans nom';
    }
    
    button.textContent = displayText;
    button.classList.add('btn', 'btn-link');
    
    button.addEventListener('click', () => {
        // affichage dans la carte
        let cardTitle;
        if (firstName || lastName) {
            cardTitle = `${firstName || 'Non renseigné'} ${lastName || 'Non renseigné'}`;
        } else if (company) {
            cardTitle = company;
        } else {
            cardTitle = 'Contact sans nom';
        }
        
        document.getElementById('prospectName').textContent = cardTitle;
        document.getElementById('prospectDetails').textContent = 
            `Email: ${prospect.email}, Téléphone: ${prospect.phone_number}, Entreprise: ${company || 'Non renseigné'}`;
        
        document.getElementById('prospectCard').style.display = 'block';
        document.getElementById('client_id').value = prospect.id;
        
        // remet a 0 les résultats de recherche
        resultsDiv.innerHTML = '';
        document.getElementById('searchProspect').value = '';
    });
    
    resultsDiv.appendChild(button);
});
});


let productIndex = 1;

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
            <div class="col-md-3">
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" name="produits[${productIndex}][quantite]" class="form-control quantite-input" value="1" min="1" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Prix unitaire HT</label>
                    <input type="number" step="0.01" name="produits[${productIndex}][prix_ht]" class="form-control prix-input" readonly>
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-product">Supprimer</button>
            </div>
        </div>
    `;
    document.getElementById('produitsList').insertAdjacentHTML('beforeend', productRow);
    productIndex++;
    updateTotals();
});

// met a jour le total
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('quantite-input') || e.target.classList.contains('produit-select')) {
        updateTotals();
    }
});

// suppr un produit
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.produit-row').remove();
        updateTotals();
    }
});

// recalculer les totaux
function updateTotals() {
    let totalHT = 0;
    document.querySelectorAll('.produit-row').forEach(row => {
        const produitSelect = row.querySelector('.produit-select');
        const prixUnitaire = parseFloat(produitSelect.selectedOptions[0]?.dataset?.prix || 0);
        const quantite = parseInt(row.querySelector('.quantite-input').value) || 1;
        const prixInput = row.querySelector('.prix-input');
        const prixTotal = prixUnitaire * quantite;

        prixInput.value = prixUnitaire.toFixed(2);
        totalHT += prixTotal;
    });

    document.getElementById('totalHT').textContent = totalHT.toFixed(2);
    document.getElementById('totalTTC').textContent = (totalHT * 1.2).toFixed(2); // TVA à 20 %
}
</script>
@endsection