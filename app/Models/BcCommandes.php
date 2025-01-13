<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BcCommandes extends Model
{
    protected $table = 'bc_commandes';
    protected $dates = ['date_commande'];

    protected $fillable = [
        'numero_commande',
        'client_id',
        'conseiller_id',
        'date_commande',
        'modalites_paiement',
        'total_ht',
        'total_ttc',
        'is_cgv_validated',
        'validatedAt',
        'isProcessed',
        'planification',
        'payment_token',
        'payment_link_expires_at',
        'installments_count'
    ];

    public function isPaid()
    {
        // vérifier si la commande est payée
        return $this->isProcessed && $this->validatedAt !== null;

    }


    public function client()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function conseiller()
    {
        return $this->belongsTo(BcUtilisateur::class, 'conseiller_id');
    }

    public function produits()
    {
        return $this->belongsToMany(Produits::class, 'bc_commande_produits', 'commande_id', 'produit_id')->withPivot('quantite', 'prix_unitaire_ht', 'prix_ht'); 
    }
}
