<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductCategoryPos extends Model
{
    use HasFactory,PreventDemoModeChanges;

    public function product()
    {
        return $this->belongsTo(ProductPos::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
