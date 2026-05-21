<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $service) {}

    public function index(): View
    {
        $settings = Setting::allKeyed();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request, string $group): JsonResponse
    {
        abort_unless(
            in_array($group, ['general','loan','bank','apk','smtp','sms','notifications','homepage','security']),
            404
        );

        $validated = $request->validate($this->rulesFor($group));

        match ($group) {
            'general'       => $this->saveGeneral($request, $validated),
            'loan'          => $this->saveLoan($validated),
            'bank'          => $this->saveBank($validated),
            'apk'           => $this->saveApk($request, $validated),
            'smtp'          => $this->saveSmtp($validated),
            'sms'           => $this->saveSms($request, $validated),
            'notifications' => $this->saveNotifications($request, $validated),
            'homepage'      => $this->saveHomepage($validated),
            'security'      => $this->saveSecurity($request, $validated),
        };

        ActivityLogService::settingsChanged($group);

        return response()->json([
            'status'  => true,
            'message' => ucfirst($group) . ' settings saved successfully.',
        ]);
    }

    // ── Private save handlers ────────────────────────────────────────────────

    private function saveGeneral(Request $request, array $data): void
    {
        $textKeys  = ['website_name', 'company_name', 'support_email', 'support_phone', 'address'];
        $imageKeys = ['company_logo', 'favicon'];

        $this->service->saveMany(array_intersect_key($data, array_flip($textKeys)), 'general');

        foreach ($imageKeys as $key) {
            if ($request->hasFile($key)) {
                $old = Setting::get($key);
                if ($old) $this->service->deleteFile($old);
                Setting::set($key, $this->service->upload($request->file($key), 'uploads/settings'), 'general');
            }
        }
    }

    private function saveLoan(array $data): void
    {
        $this->service->saveMany($data, 'loan');
    }

    private function saveBank(array $data): void
    {
        $this->service->saveMany($data, 'bank');
    }

    private function saveApk(Request $request, array $data): void
    {
        $this->service->saveMany([
            'apk_version'  => $data['apk_version'],
            'force_update' => $request->boolean('force_update') ? '1' : '0',
        ], 'apk');

        if ($request->hasFile('apk_file')) {
            $this->service->deleteFile(Setting::get('apk_file'));

            $dir = public_path('downloads');
            if (! \Illuminate\Support\Facades\File::exists($dir)) {
                \Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true);
            }
            $request->file('apk_file')->move($dir, 'easyloanpoint.apk');
            Setting::set('apk_file', 'downloads/easyloanpoint.apk', 'apk');
        }
    }

    private function saveSmtp(array $data): void
    {
        $this->service->saveMany($data, 'smtp');
    }

    private function saveSms(Request $request, array $data): void
    {
        $this->service->saveMany([
            'otp_enabled'    => $request->boolean('otp_enabled')    ? '1' : '0',
            'sms_api_key'    => $data['sms_api_key']    ?? '',
            'sms_api_secret' => $data['sms_api_secret'] ?? '',
            'sms_sender_id'  => $data['sms_sender_id']  ?? '',
        ], 'sms');
    }

    private function saveNotifications(Request $request, array $data): void
    {
        $this->service->saveMany([
            'email_notifications' => $request->boolean('email_notifications') ? '1' : '0',
            'push_notifications'  => $request->boolean('push_notifications')  ? '1' : '0',
            'sms_notifications'   => $request->boolean('sms_notifications')   ? '1' : '0',
        ], 'notifications');
    }

    private function saveHomepage(array $data): void
    {
        $this->service->saveMany($data, 'homepage');
    }

    private function saveSecurity(Request $request, array $data): void
    {
        $this->service->saveMany([
            'session_timeout'     => $data['session_timeout'],
            'login_attempt_limit' => $data['login_attempt_limit'],
            'password_min_length' => $data['password_min_length'],
            'password_uppercase'  => $request->boolean('password_uppercase') ? '1' : '0',
            'password_numbers'    => $request->boolean('password_numbers')   ? '1' : '0',
        ], 'security');
    }

    // ── Validation rules per group ───────────────────────────────────────────

    private function rulesFor(string $group): array
    {
        return match ($group) {
            'general' => [
                'website_name'  => ['required', 'string', 'max:100'],
                'company_name'  => ['required', 'string', 'max:100'],
                'company_logo'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
                'favicon'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,ico,svg', 'max:512'],
                'support_email' => ['required', 'email', 'max:100'],
                'support_phone' => ['required', 'string', 'max:20'],
                'address'       => ['nullable', 'string', 'max:500'],
            ],
            'bank' => [
                'payment_bank_name'      => ['required', 'string', 'max:100'],
                'payment_account_number' => ['required', 'string', 'max:30'],
                'payment_ifsc_code'      => ['required', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
                'payment_holder_name'    => ['required', 'string', 'max:100'],
            ],
            'loan' => [
                'min_loan_amount'       => ['required', 'numeric', 'min:1'],
                'max_loan_amount'       => ['required', 'numeric', 'gt:min_loan_amount'],
                'interest_rate'         => ['required', 'numeric', 'min:0', 'max:100'],
                'processing_fee'        => ['required', 'numeric', 'min:0', 'max:100'],
                'loan_duration_options' => ['required', 'string', 'max:200'],
            ],
            'apk' => [
                'apk_file'    => ['nullable', 'file', 'max:102400'],
                'apk_version' => ['required', 'string', 'max:20', 'regex:/^\d+\.\d+\.\d+$/'],
                'force_update'=> ['nullable', 'boolean'],
            ],
            'smtp' => [
                'mail_host'       => ['required', 'string', 'max:150'],
                'mail_port'       => ['required', 'integer', 'min:1', 'max:65535'],
                'mail_username'   => ['nullable', 'string', 'max:150'],
                'mail_password'   => ['nullable', 'string', 'max:255'],
                'mail_encryption' => ['required', 'in:tls,ssl,starttls,none'],
                'mail_from_name'  => ['required', 'string', 'max:100'],
                'mail_from_email' => ['required', 'email', 'max:100'],
            ],
            'sms' => [
                'otp_enabled'    => ['nullable', 'boolean'],
                'sms_api_key'    => ['nullable', 'string', 'max:255'],
                'sms_api_secret' => ['nullable', 'string', 'max:255'],
                'sms_sender_id'  => ['nullable', 'string', 'max:20'],
            ],
            'notifications' => [
                'email_notifications' => ['nullable', 'boolean'],
                'push_notifications'  => ['nullable', 'boolean'],
                'sms_notifications'   => ['nullable', 'boolean'],
            ],
            'homepage' => [
                'hero_title'       => ['required', 'string', 'max:200'],
                'hero_subtitle'    => ['nullable', 'string', 'max:500'],
                'download_section' => ['nullable', 'string', 'max:1000'],
                'about_section'    => ['nullable', 'string', 'max:3000'],
                'contact_info'     => ['nullable', 'string', 'max:1000'],
            ],
            'security' => [
                'session_timeout'     => ['required', 'integer', 'min:5', 'max:1440'],
                'login_attempt_limit' => ['required', 'integer', 'min:1', 'max:20'],
                'password_min_length' => ['required', 'integer', 'min:6', 'max:32'],
                'password_uppercase'  => ['nullable', 'boolean'],
                'password_numbers'    => ['nullable', 'boolean'],
            ],
        };
    }
}
