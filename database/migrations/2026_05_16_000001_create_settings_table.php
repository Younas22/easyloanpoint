<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general')->index();
            $table->timestamps();
        });

        $now = now();

        DB::table('settings')->insert([
            // ── General ────────────────────────────────────────────────────────
            ['key' => 'website_name',    'value' => 'EasyLoanPoint',                        'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_name',    'value' => 'EasyLoanPoint Pvt Ltd',                'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'company_logo',    'value' => null,                                   'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'favicon',         'value' => null,                                   'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_email',   'value' => 'support@easyloanpoint.com',            'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'support_phone',   'value' => '+91 9999999999',                       'group' => 'general',       'created_at' => $now, 'updated_at' => $now],
            ['key' => 'address',         'value' => '',                                     'group' => 'general',       'created_at' => $now, 'updated_at' => $now],

            // ── Loan ───────────────────────────────────────────────────────────
            ['key' => 'min_loan_amount',       'value' => '10000',       'group' => 'loan', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'max_loan_amount',       'value' => '500000',      'group' => 'loan', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'interest_rate',         'value' => '12',          'group' => 'loan', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'processing_fee',        'value' => '2',           'group' => 'loan', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'loan_duration_options', 'value' => '3,6,12,18,24,36', 'group' => 'loan', 'created_at' => $now, 'updated_at' => $now],

            // ── APK ────────────────────────────────────────────────────────────
            ['key' => 'apk_file',     'value' => null,    'group' => 'apk', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'apk_version',  'value' => '1.0.0', 'group' => 'apk', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'force_update', 'value' => '0',     'group' => 'apk', 'created_at' => $now, 'updated_at' => $now],

            // ── SMTP ───────────────────────────────────────────────────────────
            ['key' => 'mail_host',       'value' => 'smtp.mailtrap.io',            'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_port',       'value' => '587',                          'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_username',   'value' => '',                             'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_password',   'value' => '',                             'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_encryption', 'value' => 'tls',                          'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_from_name',  'value' => 'EasyLoanPoint',               'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_from_email', 'value' => 'no-reply@easyloanpoint.com',  'group' => 'smtp', 'created_at' => $now, 'updated_at' => $now],

            // ── SMS / OTP ──────────────────────────────────────────────────────
            ['key' => 'otp_enabled',    'value' => '1',    'group' => 'sms', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sms_api_key',    'value' => '',     'group' => 'sms', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sms_api_secret', 'value' => '',     'group' => 'sms', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sms_sender_id',  'value' => 'ELOAN','group' => 'sms', 'created_at' => $now, 'updated_at' => $now],

            // ── Notifications ─────────────────────────────────────────────────
            ['key' => 'email_notifications', 'value' => '1', 'group' => 'notifications', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'push_notifications',  'value' => '1', 'group' => 'notifications', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'sms_notifications',   'value' => '1', 'group' => 'notifications', 'created_at' => $now, 'updated_at' => $now],

            // ── Homepage ──────────────────────────────────────────────────────
            ['key' => 'hero_title',       'value' => 'Fast & Easy Loans in India',           'group' => 'homepage', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'hero_subtitle',    'value' => 'Get your loan approved in minutes',    'group' => 'homepage', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'download_section', 'value' => 'Download our app and apply instantly', 'group' => 'homepage', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about_section',    'value' => 'About EasyLoanPoint',                 'group' => 'homepage', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_info',     'value' => '',                                     'group' => 'homepage', 'created_at' => $now, 'updated_at' => $now],

            // ── Security ──────────────────────────────────────────────────────
            ['key' => 'session_timeout',     'value' => '120', 'group' => 'security', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'login_attempt_limit', 'value' => '5',   'group' => 'security', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'password_min_length', 'value' => '8',   'group' => 'security', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'password_uppercase',  'value' => '1',   'group' => 'security', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'password_numbers',    'value' => '1',   'group' => 'security', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
