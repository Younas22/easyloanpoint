<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone',
        'aadhaar_number', 'aadhaar_document',
        'pan_number', 'pan_document', 'selfie_document',
        'address', 'city', 'state', 'pincode', 'dob', 'gender',
        'employment_type', 'salary', 'status', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dob'    => 'date',
            'salary' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)->whereNotIn('status', ['rejected']);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(CustomerAssignment::class)->latestOfMany();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getMaskedAadhaarAttribute(): string
    {
        if (! $this->aadhaar_number) return '—';
        return 'XXXX-XXXX-' . substr($this->aadhaar_number, -4);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active'      => 'Active',
            'inactive'    => 'Inactive',
            'blacklisted' => 'Blacklisted',
            default       => ucfirst($this->status),
        };
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

    public function getEmploymentLabelAttribute(): string
    {
        return match($this->employment_type) {
            'salaried'      => 'Salaried',
            'self_employed' => 'Self Employed',
            'business'      => 'Business',
            'unemployed'    => 'Unemployed',
            default         => '—',
        };
    }
}
