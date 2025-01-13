<div class="container" style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; line-height: 1.6;">
    <div style="text-align: center;">
        <img src="{{ asset('sd_laucarre.png') }}" alt="Logo" style="width: 200px; margin-bottom: 20px;">
        <hr style="width: 100%; border: none; background-color: #b7b7c5; height: 2px; margin: 20px 0;">
        
        <h2 style="color: #333; margin-bottom: 15px;">Confirmation de commande</h2>
        
        <div style="background-color: #ffffff; border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-top: 20px; text-align: left;">
            <p>Bonjour {{ $firstname }},</p>
            
            <p>Nous vous remercions pour votre commande. Nous sommes ravis de vous confirmer que votre commande a été enregistrée avec succès.</p>
            
            <h3 style="color: #444; margin-top: 20px;">Détails de la commande</h3>
            
            <p>
                <strong>Numéro de commande :</strong> {{ $numero_commande }}<br>
                <strong>Date de la commande :</strong> {{ $date_commande }}<br>
                <strong>Client :</strong> {{ $firstname }} {{ $lastname }}
            </p>
            
            <div style="background-color: #f4f4f4; border-radius: 5px; padding: 15px; margin: 15px 0;">
                <h3 style="color: #444; margin-bottom: 15px;">Récapitulatif des produits</h3>
                <ul style="list-style-type: none; padding: 0;">
                    @foreach($produits as $produit)
                        <li style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                            <strong style="font-size: 16px; color: #333;">{{ $produit['nom'] }}</strong><br>
                            Prix Unitaire HT : {{ number_format($produit['prix_unitaire_ht'], 2) }} €<br>
                            Quantité : {{ $produit['quantite'] }}<br>
                            <strong>Total : {{ number_format($produit['total'], 2) }} €</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ $payment_url }}" style="display: inline-block; background-color: #4CAF50; color: white; padding: 14px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">Procéder au Paiement</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                <strong>Important :</strong> Ce lien de paiement est valable jusqu'au {{ $expires_at }}. Nous vous invitons à procéder au règlement avant cette date pour éviter toute annulation.
            </p>
        </div>
    </div>
</div>
