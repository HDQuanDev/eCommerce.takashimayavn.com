<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'usage_limit',
        'used_count',
        'description',
        'is_active'
    ];

    /**
     * Get the shops that used this referral code.
     */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
