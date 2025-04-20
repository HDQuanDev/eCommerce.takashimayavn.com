<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'seller_id',
        'amount',
        'payment_details',
        'payment_method',
        'txn_code'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
