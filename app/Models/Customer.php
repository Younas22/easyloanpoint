<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'aadhaar_number', 'pan_number',
        'address', 'city', 'state', 'pincode', 'dob', 'gender',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)->whereNotIn('status', ['rejected']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active'      => 'badge-approved',
            'inactive'    => 'badge-review',
            'blacklisted' => 'badge-rejected',
            default       => 'badge-pending',
        };
    }
}
