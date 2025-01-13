<?php

namespace App\Mail;

use App\Models\BcCommandes;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatus extends Mailable
{
    use Queueable, SerializesModels;

    protected BcCommandes $order;
    protected string $pdfPath;
    protected string $paymentToken;

    public function __construct(BcCommandes $order, string $pdfPath, string $paymentToken)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->paymentToken = $paymentToken;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Statut de la commande',
        );
    }

    public function content(): Content
    {
        $produits = $this->order->produits->map(function ($produit) {
            return [
                'nom' => $produit->nom,
                'prix_unitaire_ht' => $produit->pivot->prix_unitaire_ht,
                'quantite' => $produit->pivot->quantite,
                'total' => $produit->pivot->quantite * $produit->pivot->prix_unitaire_ht,
            ];
        });

        // Création de l'URL sécurisée pour le paiement
        $paymentUrl = route('orders.showCgv', [
            'commande' => $this->order->id,
            'token' => $this->paymentToken
        ]);

        return new Content(
            view: 'mail.orderStatus',
            with: [
                'numero_commande' => $this->order->numero_commande,
                'date_commande'  => $this->order->date_commande,
                'firstname' => $this->order->client->firstname,
                'lastname' => $this->order->client->lastname,
                'produits' => $produits,
                'total_ttc' => $this->order->total_ttc,
                'total_ht' => $this->order->total_ht,
                'modalite_paiement' => $this->order->modalites_paiement,
                'payment_url' => $paymentUrl,
                'expires_at' => $this->order->payment_link_expires_at->format('d/m/Y H:i'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->order->numero_commande . '.pdf'),
        ];
    }
}