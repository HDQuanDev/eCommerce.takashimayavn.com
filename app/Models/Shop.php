<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;
use Illuminate\Support\Facades\DB;

class Shop extends Model
{
  use PreventDemoModeChanges;


  protected $with = ['user'];



  protected $appends = ['commission_percentage'];

  public function getCommissionPercentageAttribute(){
    $commission_percentage = $this->user->commission_percentage;
    if($commission_percentage == 0){
      $commission_percentage = DB::table('shops')->where('id', $this->id)->first()->commission_percentage;
    }
    return $commission_percentage;
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function seller_package(){
    return $this->belongsTo(SellerPackage::class);
  }
  public function followers(){
    return $this->hasMany(FollowSeller::class);
  }

  /**
   * Get the referral code used at registration.
   */
  public function referralCode()
  {
    return $this->belongsTo(ReferralCode::class);
  }
}
