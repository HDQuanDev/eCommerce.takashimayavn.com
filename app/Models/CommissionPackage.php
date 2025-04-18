<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionPackage extends Model
{
    use HasFactory;
    use PreventDemoModeChanges;

    protected $fillable = [
        'user_id', 'name', 'duration', 'commission_percentage', 'description', 'image', 'status'
    ];

    public function users(){
        return $this->belongsToMany(User::class, 'commission_package_user', 'commission_package_id', 'user_id')->withPivot('price', 'start_date', 'end_date');
    }

}
