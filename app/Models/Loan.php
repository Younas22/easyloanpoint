<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'customer_id', 'assigned_hr_id', 'loan_number', 'loan_type_id', 'loan_type',
        'amount_requested', 'amount_approved', 'interest_rate',
        'tenure_months', 'repayment_days', 'purpose', 'status', 'remarks',
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

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
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
        return $this->hasMany(LoanStatusHistory::class)->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function latestPayment(): HasMany
    {
        return $this->hasMany(LoanPayment::class)->latest();
    }

    public function getEmiAmountAttribute(): ?float
    {
        if (!$this->amount_approved || !$this->interest_rate || !$this->tenure_months) {
            return null;
        }
        $r = $this->interest_rate / 12 / 100;
        $n = $this->tenure_months;
        $p = (float) $this->amount_approved;
        return $r > 0
            ? round($p * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1), 2)
            : round($p / $n, 2);
    }

    public function getTotalPayableAttribute(): ?float
    {
        $emi = $this->emi_amount;
        return $emi ? round($emi * $this->tenure_months, 2) : null;
    }

    public function getTotalInterestAttribute(): ?float
    {
        $total = $this->total_payable;
        return $total ? round($total - (float) $this->amount_approved, 2) : null;
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

    public function getReturnDateAttribute(): ?\Carbon\Carbon
    {
        if (! $this->applied_at) return null;
        return $this->applied_at->copy()->addDays($this->repayment_days ?: 6);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->return_date && now()->gt($this->return_date)
            && ! in_array($this->status, ['rejected', 'closed']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'      => 'Pending',
            'under_review' => 'Under Review',
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'disbursed'    => 'Disbursed',
            'closed'       => 'Closed',
            default        => ucfirst($this->status),
        };
    }

    public function getLoanTypeLabelAttribute(): string
    {
        if ($this->relationLoaded('loanType') && $this->loanType) {
            return $this->loanType->name;
        }
        return match($this->loan_type) {
            'personal'  => 'Personal Loan',
            'home'      => 'Home Loan',
            'business'  => 'Business Loan',
            'vehicle'   => 'Vehicle Loan',
            'education' => 'Education Loan',
            default     => $this->loan_type ? ucfirst(str_replace('_', ' ', $this->loan_type)) : '—',
        };
    }
}
