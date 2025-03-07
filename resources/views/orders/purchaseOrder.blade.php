<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de commande - {{ $commande->numero_commande }}</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            color: #362258;
            width: 100%;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            padding-bottom: 100px;
        }

        header {
            width: 100%;
            position: relative;
            margin-bottom: 30px;
        }

        .headerLeft {
            width: 50%;
            float: left;
        }

        .headerRight {
            width: 45%;
            float: right;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #362258;
            margin-bottom: 20px;
        }

        .headerRight p {
            margin: 0;
            font-size: 11px;
        }

        .companyInfo {
            margin-bottom: 20px;
            overflow: hidden;
        }

        .companyInfo img {
            float: left;
            width: 200px;
            margin-right: 20px;
        }

        .contact {
            float: left;
        }

        .contact p {
            margin: 0;
            font-size: 11px;
        }

        .orderInfo p {
            margin: 5px 0;
            font-size: 11px;
        }

        .orderInfo .numero {
            display: block;
            margin-bottom: 5px;
        }

        .orderInfo .date {
            display: block;
        }

        .headerRight h2 {
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .clear {
            clear: both;
        }

        .orderTable {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
        }

        .orderTable th {
            background-color: #362258;
            color: white;
            padding: 10px;
            text-align: left;
        }

        .orderTable td, .orderTable th {
            padding: 8px;
            text-align: center;
            border-right: 2px dotted #362258;
        }

        .orderTable td:last-child,
        .orderTable th:last-child {
            border-right: none;
        }

        .orderPayment {
            width: 300px;
            margin-left: auto;
            margin-bottom: 20px;
        }

        .paymentTable {
            width: 100%;
            border-collapse: collapse;
        }

        .paymentTable tr td {
            padding: 5px 0;
        }

        .paymentTable tr.total-row {
            border-top: 2px solid #362258;
            padding-top: 10px;
            margin-top: 5px;
        }

        .paymentTable td:last-child {
            text-align: right;
        }

        .orderPayment div {
            overflow: hidden; 
            margin-bottom: 5px;
        }

        .orderPayment p {
            float: left;
            margin: 0;
        }

        .orderPayment p:last-child {
            float: right;
        }

        .orderPayment .total-row {
            border-top: 2px solid #362258;
            padding-top: 10px;
            margin-top: 5px;
        }

        .orderValidation {
            background-color: #b7b7c5;
            padding: 5px 30px;
        }

        .orderValidationDate {
            margin-bottom: 20px;
        }

        .orderValidationDate p {
            display: inline-block;
            margin-right: 100px;
        }

        footer {
            width: 100%;
            text-align: center;
            border-top: 2px solid #362258;
            padding-top: 20px;
            position: absolute;
            bottom: 0;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <header>
        <div class="headerLeft">
            <div class="companyInfo">
                <img src="{{ asset('sd_laucarre.png') }}" alt="Logo">
                <div class="contact">
                    <p><strong>SAS L AU CARRE</strong></p>
                    <p>Granoux 15 700 PLEAUX</p>
                    <p>Mail : dpeyral@direo.fr</p>
                    <p>Siret : 804 485 167 000 22</p>
                </div>
            </div>
            <div class="orderInfo">
                <p class="numero">N° de commande : {{ $commande->numero_commande }}</p>
                <p class="date">Date : {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</p>
                <p>Conseiller : {{ $commande->conseiller->name }}</p>
            </div>
        </div>
        <div class="headerRight">
            <div class="title">Bon de commande</div>
            <h2>Client :</h2>
            @if($commande->client->company)
            <p>Entreprise : {{ $commande->client->company }}</p>
            <p>Numéro siret : {{ $commande->client->siret }}</p>
            @else
            <p><strong>{{ $commande->client->firstname }} {{ $commande->client->lastname }}</strong></p>
            @endif
            <p>{{ $commande->client->address }}</p>
            <p>Code postal : {{ $commande->client->postal_code }}</p>
            <p>Ville : {{ $commande->client->city }}</p>
            <p>Tél : {{ $commande->client->phone_number }}</p>
            <p>Mail : {{ $commande->client->email }}</p>
        </div>
        <div class="clear"></div>
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
                <td>{{ number_format($produit->pivot->quantite) }}</td>
                <td>{{ number_format($produit->pivot->prix_unitaire_ht, 2) }} €</td>
                <td>{{ number_format($produit->pivot->prix_ht, 2) }} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="orderPayment">
        <table class="paymentTable">
            <tr>
                <td><strong>Total HT :</strong></td>
                <td>{{ number_format($commande->total_ht, 2) }} €</td>
            </tr>
            <tr>
                <td><strong>TVA 20% :</strong></td>
                <td>{{ number_format($commande->total_ht * 0.2, 2) }} €</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total TTC :</strong></td>
                <td>{{ number_format($commande->total_ttc, 2) }} €</td>
            </tr>
        </table>
    </div>

    <!--<div class="orderValidation">
        <div class="orderValidationDate">
            <p>Fait le : {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}</p>
        </div>
    </div>-->

    <footer>L AU CARRE - SAS au capital de 2506 € - Granoux 15 700 PLEAUX - Siret : 804 485 167 000 22 <br> APE : 8299Z - RCS AURILLAC B 804 485 167</footer>
</body>
</html>
