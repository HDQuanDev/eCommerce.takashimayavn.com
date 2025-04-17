<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPackagePayment extends Model
{
    protected $fillable = [
        'user_id',
        'seller_package_id',
        'payment_method',
        'payment_details',
        'approval',
        'offline_payment',
        'offline_payment_proof'
    ];

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
