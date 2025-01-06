<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BcCommandeProduits extends Model
{
    protected $table = 'bc_commande_produit';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire_ht',
    ];

    public function commande()
    {
        return $this->belongsTo(BcCommandes::class, 'commande_id');
    }

    public function produit()
    {
        return $this->belongsTo(Produits::class, 'produit_id');
    }
}
