<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Model;

class SellerDepositRequest extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function payment_method()
    {
        return $this->belongsTo(V2PaymentMethod::class, 'payment_method_id', 'id');
    }
}
