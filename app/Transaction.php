<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'title', 'reference', 'amount', 'payment_method_id', 'type', 'client_id', 'user_id', 'sale_id', 'provider_id', 'transfer_id'
    ];

    public function method() {
        return $this->belongsTo('App\PaymentMethod', 'payment_method_id');
    }

    public function provider() {
        return $this->belongsTo('App\Provider');
    }

    public function sale() {
        return $this->belongsTo('App\Sale');
    }

    public function client() {
        return $this->belongsTo('App\Client');
    }

    public function transfer ()
    {
        return $this->belongsTo('App\Transfer');
    }
}
