<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpVerification extends Model
{
    protected $fillable = [
        'phone',
        'otp_hash',
        'purpose',
        'attempts',
        'expires_at',
        'verified_at',
        'consumed_at',
        'verification_token_hash',
        'verification_token_expires_at',
        'last_sent_at',
        'ip_address',
    ];

    protected $hidden = [
        'otp_hash',
        'verification_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'                     => 'datetime',
            'verified_at'                     => 'datetime',
            'consumed_at'                     => 'datetime',
            'verification_token_expires_at'   => 'datetime',
            'last_sent_at'                    => 'datetime',
            'attempts'                        => 'integer',
        ];
    }

    // ── State checks ─────────────────────────────────────────────────────────

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function hasExceededAttempts(int $maxAttempts): bool
    {
        return $this->attempts >= $maxAttempts;
    }

    public function isTokenExpired(): bool
    {
        return $this->verification_token_expires_at === null
            || Carbon::now()->greaterThanOrEqualTo($this->verification_token_expires_at);
    }

    public function isWithinCooldown(int $cooldownSeconds): bool
    {
        return $this->last_sent_at !== null
            && Carbon::now()->lessThan($this->last_sent_at->copy()->addSeconds($cooldownSeconds));
    }

    public function cooldownSecondsRemaining(int $cooldownSeconds): int
    {
        if ($this->last_sent_at === null) {
            return 0;
        }

        $retryAt = $this->last_sent_at->copy()->addSeconds($cooldownSeconds);

        return max(0, (int) Carbon::now()->diffInSeconds($retryAt, false));
    }
}
