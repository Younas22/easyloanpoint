<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBankAccount extends Model
{
    protected $fillable = [
        'customer_id', 'sort_order', 'bank_name', 'account_number', 'ifsc_code', 'holder_name',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getMaskedAccountAttribute(): string
    {
        if (strlen($this->account_number) <= 4) return $this->account_number;
        return str_repeat('X', strlen($this->account_number) - 4) . substr($this->account_number, -4);
    }
}
