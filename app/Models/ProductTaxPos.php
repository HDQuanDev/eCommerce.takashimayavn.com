<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ProductTaxPos extends Model
{
    use PreventDemoModeChanges;
    protected $table = 'product_taxes_pos';
    //
}
