<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BcMandats extends Model
{
    protected $fillable = [
        'iban',
        'bic',
        'reference_unique'
     ];
}
