<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerAdsPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ads_package_id',
        'status',
        'reach',
        'price',
    ];

    public function adsPackage()
    {
        return $this->belongsTo(AdsPackage::class);
    }
}
