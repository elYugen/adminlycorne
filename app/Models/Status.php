<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $dates = ['order_date'];

    public function order()
    {
        return $this->belongsTo(BcCommandes::class);
    }

}
