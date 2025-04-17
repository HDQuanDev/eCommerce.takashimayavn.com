<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductStockPos extends Model
{
    use PreventDemoModeChanges;

    protected $table = 'product_stocks_pos';

    protected $fillable = ['product_id', 'variant', 'sku', 'price', 'qty', 'image'];
    //
    public function product(){
    	return $this->belongsTo(ProductPos::class);
    }

    public function wholesalePrices() {
        return $this->hasMany(WholesalePrice::class);
    }
}
