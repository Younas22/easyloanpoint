<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'role', 'action', 'description', 'ip_address', 'user_agent',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'System']);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    public function getBadgeColorAttribute(): string
    {
        return match($this->action) {
            'login'               => 'green',
            'logout'              => 'gray',
            'loan_approved'       => 'green',
            'loan_rejected'       => 'red',
            'loan_applied',
            'loan_status_updated' => 'blue',
            'customer_created',
            'customer_updated',
            'customer_assigned'   => 'indigo',
            'document_uploaded',
            'document_verified'   => 'purple',
            'settings_changed'    => 'orange',
            'apk_updated'         => 'violet',
            'profile_updated'     => 'yellow',
            'hr_created'          => 'teal',
            'system_tool_run'     => 'red',
            default               => 'slate',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'login'               => 'Login',
            'logout'              => 'Logout',
            'customer_created'    => 'Customer Created',
            'customer_updated'    => 'Customer Updated',
            'customer_assigned'   => 'Customer Assigned',
            'loan_applied'        => 'Loan Applied',
            'loan_approved'       => 'Loan Approved',
            'loan_rejected'       => 'Loan Rejected',
            'loan_status_updated' => 'Loan Status Updated',
            'document_uploaded'   => 'Document Uploaded',
            'document_verified'   => 'Document Verified',
            'settings_changed'    => 'Settings Changed',
            'apk_updated'         => 'APK Updated',
            'profile_updated'     => 'Profile Updated',
            'hr_created'          => 'HR Created',
            'system_tool_run'     => 'System Tool Run',
            default               => ucwords(str_replace('_', ' ', $this->action)),
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'    => 'Admin',
            'hr'       => 'HR',
            'customer' => 'Customer',
            default    => 'System',
        };
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Edge'))    return 'Edge';
        return 'Unknown';
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);
        return $query;
    }

    // ── Constants ────────────────────────────────────────────────────────────

    public static function actions(): array
    {
        return [
            'login', 'logout', 'customer_created', 'customer_updated',
            'customer_assigned', 'loan_applied', 'loan_approved', 'loan_rejected',
            'loan_status_updated', 'document_uploaded', 'document_verified',
            'settings_changed', 'apk_updated', 'profile_updated', 'hr_created',
            'system_tool_run',
        ];
    }
}
