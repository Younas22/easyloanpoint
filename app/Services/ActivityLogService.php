<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Record an activity log entry.
     *
     * @param  string       $action      One of ActivityLog::actions()
     * @param  string       $description Human-readable description
     * @param  int|null     $userId      Defaults to currently authenticated user
     * @param  string|null  $role        Defaults to authenticated user's role
     */
    public static function log(
        string  $action,
        string  $description,
        ?int    $userId = null,
        ?string $role   = null
    ): void {
        try {
            $request = app(Request::class);
            $user    = Auth::user();

            ActivityLog::create([
                'user_id'     => $userId  ?? $user?->id,
                'role'        => $role    ?? $user?->role,
                'action'      => $action,
                'description' => $description,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        } catch (\Throwable) {
            // Never let logging break the application
        }
    }

    // ── Convenience wrappers ─────────────────────────────────────────────────

    public static function loginSuccess(string $userName): void
    {
        self::log('login', "{$userName} logged in successfully.");
    }

    public static function logout(string $userName): void
    {
        self::log('logout', "{$userName} logged out.");
    }

    public static function customerCreated(string $customerName): void
    {
        self::log('customer_created', "Customer \"{$customerName}\" was created.");
    }

    public static function customerUpdated(string $customerName): void
    {
        self::log('customer_updated', "Customer \"{$customerName}\" profile updated.");
    }

    public static function customerAssigned(string $customerName, string $hrName): void
    {
        self::log('customer_assigned', "Customer \"{$customerName}\" assigned to HR \"{$hrName}\".");
    }

    public static function loanApplied(string $loanNumber, string $customerName): void
    {
        self::log('loan_applied', "Loan {$loanNumber} applied for customer \"{$customerName}\".");
    }

    public static function loanStatusUpdated(string $loanNumber, string $from, string $to): void
    {
        $action = match($to) {
            'approved' => 'loan_approved',
            'rejected' => 'loan_rejected',
            default    => 'loan_status_updated',
        };
        self::log($action, "Loan {$loanNumber} status changed from \"{$from}\" to \"{$to}\".");
    }

    public static function documentUploaded(string $loanNumber, string $docName): void
    {
        self::log('document_uploaded', "Document \"{$docName}\" uploaded for loan {$loanNumber}.");
    }

    public static function documentVerified(string $loanNumber, string $docName, string $status): void
    {
        self::log('document_verified', "Document \"{$docName}\" marked as \"{$status}\" for loan {$loanNumber}.");
    }

    public static function profileUpdated(string $userName): void
    {
        self::log('profile_updated', "User \"{$userName}\" updated their profile.");
    }

    public static function settingsChanged(string $group): void
    {
        self::log('settings_changed', "Settings group \"{$group}\" was updated.");
    }

    public static function apkUpdated(string $version = ''): void
    {
        $label = $version ? "v{$version}" : 'latest';
        self::log('apk_updated', "Android APK ({$label}) was uploaded.");
    }

    public static function hrCreated(string $hrName): void
    {
        self::log('hr_created', "HR user \"{$hrName}\" was created.");
    }
}
