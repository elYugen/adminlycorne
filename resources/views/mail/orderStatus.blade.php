<div style="display:flex; justify-content:center; align-items:center; flex-direction:column">

    <p>Récapitulatif Commande : <strong>{{ $numero_commande }}</strong> pour {{ $firstname }} {{ $lastname }}</p>
    <br>
    <p>Date de la commande : <strong>{{ $date_commande }}</strong></p>
    <br>
    <p>Contenu de la commande :</p>
    <br>
    <ul>
        @foreach($produits as $produit)
            <li>
                <strong>{{ $produit['nom'] }}</strong><br>
                Prix Unitaire HT : {{ number_format($produit['prix_unitaire_ht'], 2) }} €<br>
                Quantité : {{ $produit['quantite'] }}<br>
                Total : {{ number_format($produit['total'], 2) }} €
            </li>
        @endforeach
    </ul>
    <br>
    <p>Moyen de paiement : <strong>{{ $modalite_paiement }}</strong></p>
    <br>
    <a href="{{ $validate_cgv_url }}">Valider les CGV et la commande</a>
</div>