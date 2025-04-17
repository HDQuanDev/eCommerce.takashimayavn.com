<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductTranslationPos extends Model
{
  use PreventDemoModeChanges;
    protected $table = 'product_translations_pos';

    protected $fillable = ['product_id', 'name', 'unit', 'description', 'lang'];

    public function product(){
      return $this->belongsTo(Product::class);
    }
}
