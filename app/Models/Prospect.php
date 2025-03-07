<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Prospect extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'bc_prospects';

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'gender',
        'city',
        'address',
        'postal_code',
        'company',
        'siret',
        'deleted'  
    ];

    public function scopeActive($query)
    {
        return $query->where('deleted', 0);
    }
}
