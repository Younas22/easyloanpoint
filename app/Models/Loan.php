<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Loan extends Model
{
    protected $fillable = [
        'customer_id', 'assigned_hr_id', 'loan_number', 'loan_type',
        'amount_requested', 'amount_approved', 'interest_rate',
        'tenure_months', 'purpose', 'status', 'remarks',
        'applied_at', 'reviewed_at', 'disbursed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_requested' => 'decimal:2',
            'amount_approved'  => 'decimal:2',
            'interest_rate'    => 'decimal:2',
            'applied_at'       => 'datetime',
            'reviewed_at'      => 'datetime',
            'disbursed_at'     => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Loan $loan) {
            if (empty($loan->loan_number)) {
                $loan->loan_number = static::generateLoanNumber();
            }
        });
    }

    public static function generateLoanNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = 'ELP-' . $year . '-';
        $latest = static::where('loan_number', 'like', $prefix . '%')->max('loan_number');
        $seq    = $latest ? ((int) substr($latest, strlen($prefix)) + 1) : 1;
        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedHR(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_hr_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LoanStatusHistory::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending'      => 'badge-pending',
            'under_review' => 'badge-review',
            'approved'     => 'badge-approved',
            'rejected'     => 'badge-rejected',
            'disbursed'    => 'badge-disbursed',
            default        => 'badge-pending',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'      => 'Pending',
            'under_review' => 'Under Review',
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'disbursed'    => 'Disbursed',
            default        => ucfirst($this->status),
        };
    }

    public function getLoanTypeLabelAttribute(): string
    {
        return match($this->loan_type) {
            'personal'  => 'Personal Loan',
            'home'      => 'Home Loan',
            'business'  => 'Business Loan',
            'vehicle'   => 'Vehicle Loan',
            'education' => 'Education Loan',
            default     => ucfirst($this->loan_type),
        };
    }
}
