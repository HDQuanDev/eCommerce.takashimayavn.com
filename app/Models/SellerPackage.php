<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPackage extends Model
{
    protected $table = 'seller_packages';

    protected $fillable = [
        'name',
        'amount',
        'product_upload_limit',
        'duration',
        'logo',
        'status'
    ];

    public $timestamps = false;

    const CREATED_AT = 'create_date';
    const UPDATED_AT = null;

    public function logo()
    {
        return $this->belongsTo(Upload::class, 'logo');
    }

    public function seller_package_payments()
    {
        return $this->hasMany(SellerPackagePayment::class);
    }

    public function seller_package_translations()
    {
        return $this->hasMany(SellerPackageTranslation::class);
    }

    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? app()->getLocale() : $lang;
        $seller_package_translation = $this->seller_package_translations->where('lang', $lang)->first();

        return $seller_package_translation != null ? $seller_package_translation->$field : $this->$field;
    }
}
