<!-- Modal de création de commande -->
<div class="modal fade" id="commandeModal" tabindex="-1" aria-labelledby="commandeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commandeModalLabel">Créer une commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="commandeForm" action="{{ route('orders.create') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="commande_id" id="commande_id">

                    <!-- Client -->
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select name="client_id" id="client_id" class="form-select" required>
                            <option value="">Sélectionnez un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->firstname }} {{ $client->lastname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Conseiller -->
                    <div class="mb-3">
                        <label for="conseiller_id" class="form-label">Conseiller</label>
                        <select name="conseiller_id" id="conseiller_id" class="form-select" required>
                            <option value="">Sélectionnez un conseiller</option>
                            @foreach($conseillers as $conseiller)
                                <option value="{{ $conseiller->id }}">{{ $conseiller->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Modalité de paiement -->
                    <div class="mb-3">
                        <label for="modalite_paiement" class="form-label">Modalité de paiement</label>
                        <select name="modalite_paiement" id="modalite_paiement" class="form-select" required>
                            <option value="">Sélectionnez une modalité</option>
                            <option value="prelevement">Prélèvement</option>
                            <option value="virement">Virement</option>
                            <option value="cheque">Chèque</option>
                        </select>
                    </div>

                    <!-- Date de commande -->


                    <!-- Produits -->
                    <div id="produitsList">
                        <h6>Produits</h6>
                        <div class="row mb-2 produit-row">
                            <div class="col-md-4">
                                <select name="produits[0][produit_id]" class="form-select produit-select" required>
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_ht }}">
                                            {{ $produit->nom }} - {{ $produit->prix_ht }} €
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Quantité</label>
                                <input type="number" name="produits[0][quantite]" class="form-control quantite-input" value="1" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label>Prix unitaire HT</label>
                                <input type="number" step="0.01" name="produits[0][prix_ht]" class="form-control prix-input" readonly>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm remove-product">Supprimer</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary mt-2" id="addProductRow">Ajouter un produit</button>

                    <!-- Totaux -->
                    <div class="mt-3">
                        <strong>Total HT:</strong> <span id="totalHT">0.00</span> €
                        <br>
                        <strong>Total TTC:</strong> <span id="totalTTC">0.00</span> €
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="submit" form="commandeForm" class="btn btn-primary">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>

<script>
    let productIndex = 1;

// Ajouter une ligne de produit
document.getElementById('addProductRow').addEventListener('click', function () {
    const productRow = `
        <div class="row mb-2 produit-row">
            <div class="col-md-4">
                <select name="produits[${productIndex}][produit_id]" class="form-select produit-select" required>
                    <option value="">Sélectionnez un produit</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_ht }}">
                            {{ $produit->nom }} - {{ $produit->prix_ht }} €
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Quantité</label>
                <input type="number" name="produits[${productIndex}][quantite]" class="form-control quantite-input" value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <label>Prix unitaire HT</label>
                <input type="number" step="0.01" name="produits[${productIndex}][prix_ht]" class="form-control prix-input" readonly>
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

// Met à jour les totaux
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('quantite-input') || e.target.classList.contains('produit-select')) {
        updateTotals();
    }
});

// Supprime une ligne de produit
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.produit-row').remove();
        updateTotals();
    }
});

// Fonction pour recalculer les totaux
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
    document.getElementById('totalTTC').textContent = (totalHT * 1.2).toFixed(2); // Exemple avec TVA à 20 %
}
</script>