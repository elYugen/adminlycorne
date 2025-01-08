<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bon de commande - {{ $commande->numero_commande }} {{ $commande->client->firstname }} {{ $commande->client->lastname }}</title>
</head>
<body>
    <div style="display:flex; justify-content:center; align-items:center; flex-direction:column">

        <p>Récapitulatif Commande : <strong>{{ $commande->numero_commande }}</strong> pour {{ $commande->client->firstname }} {{ $commande->client->lastname }}</p>
        <br>
        <p>Date de la commande : <strong>{{ $commande->date_commande }}</strong></p>
        <br>
        <p>Contenu de la commande :</p>
        <br>
        <ul>
            @foreach($commande->produits as $produit)
                <li>
                    <strong>{{ $produit->nom }}</strong><br>
                    Prix Unitaire HT : {{ number_format($produit->prix_unitaire_ht, 2) }} €<br>
                    Quantité : {{ $produit->quantite }}<br>
                    Total : {{ number_format($produit->total, 2) }} €
                </li>
            @endforeach
        </ul>
        <br>
        <p>Total HT : <strong>{{ number_format($commande->total_ht, 2) }} €</strong></p>
        <p>Total TTC : <strong>{{ number_format($commande->total_ttc, 2) }} €</strong></p>
        <br>
    </div>
</body>
</html>