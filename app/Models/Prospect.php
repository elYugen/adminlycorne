<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Prospect extends Model
{
     /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory, Notifiable;

     protected $table = 'bc_prospects';

     /**
      * The attributes that are mass assignable.
      *
      * @var list<string>
      */
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
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trials_ends_at',
    ];
}
