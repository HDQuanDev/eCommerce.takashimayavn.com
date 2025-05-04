<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reach',
        'price',
        'status',
    ];

    public function sellerAdsPackages()
    {
        return $this->hasMany(SellerAdsPackage::class);
    }
}
