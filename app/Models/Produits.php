<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produits extends Model
{
    protected $table = 'bc_produits';

    public function commandes()
{
    return $this->belongsToMany(
        BcCommandes::class, 'bc_commande_produits', 'produit_id', 'commande_id')->withPivot('quantite', 'prix_unitaire_ht');
}
}
