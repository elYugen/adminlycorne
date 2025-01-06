<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BcCommandes extends Model
{
    protected $table = 'bc_commandes';

    protected $fillable = [
        'numero_commande',
        'client_id',
        'conseiller_id',
        'date_commande',
        'modalites_paiement',
        'total_ht',
        'total_ttc',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function conseiller()
    {
        return $this->belongsTo(BcConseiller::class, 'conseiller_id');
    }

    public function produits()
    {
        return $this->hasMany(BcCommandeProduits::class, 'commande_id');
    }
}
