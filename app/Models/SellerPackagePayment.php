<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPackagePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function seller_package()
    {
        return $this->belongsTo(SellerPackage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
