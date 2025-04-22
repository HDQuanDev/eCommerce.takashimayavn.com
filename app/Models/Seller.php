<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Seller extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = [
    'user_id',
    'seller_package_id',
    'rating',
    'num_of_reviews',
    'num_of_sale',
    'verification_status',
    'verification_info',
    'cash_on_delivery_status',
    'product_upload_limit',
    'admin_to_pay',
    'bank_name',
    'bank_acc_name',
    'bank_acc_no',
    'bank_routing_no',
    'bank_payment_status',
  ];
  protected $with = ['user', 'user.shop'];

  public function user(){
  	return $this->belongsTo(User::class);
  }

  public function payments(){
  	return $this->hasMany(Payment::class);
  }

  public function seller_package(){
    return $this->belongsTo(SellerPackage::class);
}
}
