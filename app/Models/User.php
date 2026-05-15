<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'status', 'profile_image',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function assignedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'assigned_hr_id');
    }

    public function createdCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(LoanStatusHistory::class, 'changed_by');
    }

    public function customerAssignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class, 'hr_id');
    }

    // ── Role helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHR(): bool
    {
        return $this->role === 'hr';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === true;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'    => 'Administrator',
            'hr'       => 'HR Manager',
            'customer' => 'Customer',
            default    => ucfirst($this->role),
        };
    }
}
