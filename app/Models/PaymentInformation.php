<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentInformation extends Model
{
    protected $fillable = [
        'user_id', 
        'card_number', 
        'name_on_card', 
        'expiry_date', 
        'cvv'
    ];

    /**
     * Get the user that owns the payment information.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mask the card number for display
     */
    public function getDecryptedCardNumberAttribute()
    {
        if (!$this->card_number) {
            return null;
        }
        
        try {
            $decrypted = Crypt::decrypt($this->card_number);
            // Format card number as XXXX XXXX XXXX XXXX
            return implode(' ', str_split($decrypted, 4));
        } catch (\Exception $e) {
            return '•••• •••• •••• ••••'; // Return placeholder if decryption fails
        }
    }

    public function getMaskedCardNumberAttribute()
    {
        if (!$this->card_number) {
            return null;
        }
        
        try {
            $decrypted = Crypt::decrypt($this->card_number);
            // Only show last 4 digits
            return '•••• •••• •••• ' . substr($decrypted, -4);
        } catch (\Exception $e) {
            return '•••• •••• •••• ••••'; // Return placeholder if decryption fails
        }
    }

    public function getDecryptedCvvAttribute()
    {
        if (!$this->cvv) {
            return null;
        }
        
        try {
            return Crypt::decrypt($this->cvv);
        } catch (\Exception $e) {
            return '***'; // Return placeholder if decryption fails
        }
    }
    
    /**
     * Format expiry date for display
     */
    public function getFormattedExpiryDateAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        // Format MM/YY if needed
        return $this->expiry_date;
    }
}