<?php

namespace App\Mail;

use App\Models\BcCommandes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class OrderStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected BcCommandes $order;
    protected string $pdfPath;

    public function __construct(BcCommandes $order, string $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Statut de la commande',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // liste des produits acheté par le client
        $produits = $this->order->produits->map(function ($produit) {
            return [
                'nom' => $produit->nom,
                'prix_unitaire_ht' => $produit->pivot->prix_unitaire_ht,
                'quantite' => $produit->pivot->quantite,
                'total' => $produit->pivot->quantite * $produit->pivot->prix_unitaire_ht,
            ];
        });

        return new Content(
            view: 'mail.orderStatus',
            with: [ // permet de choisir quel donnée envoyé plus précisément plutot que de tout envoyer en en public dans le construct
                'numero_commande' => $this->order->numero_commande, // récupère dans la variable order le numero de commande  
                'date_commande'  => $this->order->date_commande,        
                'firstname' => $this->order->client->firstname, // récupère le prénom du client
                'lastname' => $this->order->client->lastname, // récupère le nom du client
                'produits' => $produits,
                'total_ttc' => $this->order->total_ttc,
                'total_ht' => $this->order->total_ht,
                'modalite_paiement' => $this->order->modalites_paiement,
                'validate_cgv_url' => route('orders.showCgv', $this->order->id), 
        
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {

        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->order->numero_commande . '.pdf'),
        ];
    }
}
