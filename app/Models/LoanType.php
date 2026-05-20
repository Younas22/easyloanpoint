<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    protected $fillable = [
        'name', 'description', 'amount', 'repayment_days', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order')->orderBy('id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
