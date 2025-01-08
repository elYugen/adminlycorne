<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de commande - {{ $commande->numero_commande }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        color: #362258;
        max-width: 90%;
        margin: 0 auto;
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        min-height: 100vh;
    }

    header {
        display: flex;
        flex-direction: row;
        justify-content: space-between; 
    }

    .headerLeft {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .headerLeft .companyInfo {
        display: flex;
        flex-direction: row;
        gap: 50px;
    }

    .headerRight {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .headerRight h2 {
        text-decoration: underline;
    }

    .headerRight p {
        margin: 0;
    }

    hr {
        border: none;
        height: 2px;
        background-color: #362258;
        margin: 20px 0;
    }

    footer {
        margin-top: auto;
        padding: 20px 0;
        text-align: center;
        width: 100%;
    }

    footer p {
        font-size: 0.9em;
        line-height: 1.4;
    }

    .orderTable {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    }

    .orderTable th {
        background-color: #362258;
        color: white;
        padding: 10px;
        text-align: left;
        border-bottom: none;
    }

    .orderTable td, .orderTable th {
        padding: 8px;
        border-bottom: none;
        text-align: center;
    }

    .orderTable td:not(:last-child),
    .orderTable th:not(:last-child) {
        border-right: 2px dotted #362258;
    }

    .orderTable tr:last-child td {
        border-bottom: none;
    }

    .orderValidation {
        background-color: #b7b7c5;
        display: flex;
        flex-direction: column;
        padding: 20px; 
        padding-left: 85px; 
    }

    .orderValidationDate {
        display: flex;
        flex-direction: row;
        gap: 200px;
    }

    .orderValidationDate p,
    .orderValidationClient p {
        margin: 0; 
    }

    .orderValidationClient {
        margin-top: 20px; 
    }

    .orderPayment {
        display: flex;
        flex-direction: column;
        gap: 5px;
        align-items: flex-end;
        margin-left: auto;
        width: 300px;
    }

    .orderPayment div {
        display: flex;
        justify-content: space-between;
        width: 100%;
        padding: 5px 0;
    }

    .orderPayment p {
        margin: 0;
    }

    .orderPayment .total-row {
        border-top: 2px solid #362258;
        margin-top: 5px;
        padding-top: 10px;
    }

</style>
</head>
<body>
    <header>
        <div class="headerLeft">
            <div class="companyInfo">
                <img src="{{ asset('sd_laucarre.png') }}" alt="Logo" style="width: 50%;">
                <div class="contact">
                    <p><strong>SAS L AU CARRE</strong></p>
                    <p>Granoux 15 700 PLEAUX</p>
                    <br>
                    <p>Mail : dpeyral@direo.fr</p>
                    <p>Sitet : 804 485 167 000 22</p>
                </div>
            </div>
            <div class="orderInfo">
                <p>N° de commande / Date : {{ $commande->numero_commande }} {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }} </p>
                <p>Conseiller : {{ $commande->conseiller->name }}</p>
            </div>
        </div>
        <div class="headerRight">
            <h2>Bon de commande</h2>
            <p>Nom du client : {{ $commande->client->lastname }} {{ $commande->client->firstname }}</p>
            <p>Adresse : {{ $commande->client->address }}</p>
            <p>Code postal : {{ $commande->client->postal_code }}<p>
            <p>Ville : ......................................</p>
            <p>Numéro siret : {{ $commande->client->siret }}</p>
            <p>Tél : {{ $commande->client->phone_number }}</p>
            <p>Mail : {{ $commande->client->email }}</p>
        </div>
    </header>

    <table class="orderTable">
        <thead>
            <tr>
                <th>PRODUITS</th>
                <th>DESCRIPTION</th>
                <th>Qté</th>
                <th>Prix unitaire HT</th>
                <th>Prix total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->produits as $produit)
            <tr>
                <td>{{ $produit->nom }}</td>
                <td>{{ $produit->description }}</td>
                <td>{{ $produit->quantite }}</td>
                <td>{{ number_format($produit->prix_unitaire_ht, 2) }} €</td>
                <td>{{ number_format($produit->total, 2) }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="orderPayment">
        <div>
            <p><strong>Total HT :</strong></p>
            <p>{{ number_format($commande->total_ht, 2) }} €</p>
        </div>
        <div>
            <p><strong>TVA 20% :</strong></p>
            <p>{{ number_format($commande->total_ht * 0.2, 2) }} €</p>
        </div>
        <div class="total-row">
            <p><strong>Total TTC :</strong></p>
            <p>{{ number_format($commande->total_ttc, 2) }} €</p>
        </div>
    </div>

    <div class="orderValidation">
            <div class="orderValidationDate">
                <p>Fait le {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }} :</p> <p>à* :</p>
            </div>
            <div class="orderValidationClient">
                <p>Nom, prénom* : {{ $commande->client->lastname }} {{ $commande->client->firstname }}</p>
            </div>
    </div>

    <footer>
        <hr>
        <p><strong>L AU CARRE - SAS au capital de 2506 € - Granoux 15 700 PLEAUX - Siret : 804 485 167 000 22 - APE : 8299Z - RCS AURILLAC B 804 485 167</strong></p>
    </footer>
</body>
</html>