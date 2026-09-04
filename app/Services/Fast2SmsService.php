<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Thin wrapper around the Fast2SMS Quick SMS API (route=q).
 *
 * Docs: https://docs.fast2sms.com/reference/quick-sms
 *   GET https://www.fast2sms.com/dev/bulkV2
 *   Header + query param "authorization": <api key> (sent both ways — some
 *   accounts only honor the query parameter despite the header being the
 *   documented contract)
 *   Query: route=q, message=<text>, numbers=<comma separated 10-digit numbers>
 *
 * The API key is resolved from, in order: the "API Key" field on the
 * SMS / OTP tab of Admin → Settings (stored in the settings table as
 * sms_api_key — editable without server/file access), then the
 * FAST2SMS_API_KEY .env value as a fallback for first-time setup. It is
 * never returned to callers or logged either way.
 */
class Fast2SmsService
{
    private const ENDPOINT = 'https://www.fast2sms.com/dev/bulkV2';

    private const TIMEOUT_SECONDS = 10;

    /**
     * Send a plain-text SMS to a single Indian mobile number.
     *
     * Returns true only when Fast2SMS confirms the message was accepted.
     * Never throws for provider/network failures — callers get a clean
     * boolean and details are sent to the log (without the API key or
     * the message body, which may contain a live OTP).
     */
    public function send(string $phoneNumber, string $message): bool
    {
        $apiKey = Setting::get('sms_api_key') ?: config('services.fast2sms.api_key');

        if (blank($apiKey)) {
            Log::error('Fast2SMS: API key is not configured (set it in Admin → Settings → SMS / OTP, or FAST2SMS_API_KEY in .env).');

            return false;
        }

        try {
            // authorization is sent BOTH as a header (per the documented
            // contract) and as a query parameter (confirmed working against
            // this account) — some Fast2SMS accounts/edge setups only honor
            // one or the other.
            $response = Http::withHeaders([
                    'authorization' => $apiKey,
                ])
                ->timeout(self::TIMEOUT_SECONDS)
                ->get(self::ENDPOINT, [
                    'authorization' => $apiKey,
                    'route'         => 'q',
                    'message'       => $message,
                    'numbers'       => $phoneNumber,
                    'language'      => 'english',
                    'flash'         => 0,
                ]);
        } catch (Throwable $e) {
            Log::error('Fast2SMS: request failed.', [
                'phone' => $this->maskPhone($phoneNumber),
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        if ($response->failed()) {
            // The response body at this point is Fast2SMS's own error page/
            // message (never our request), so it's safe to log in full —
            // it's usually the clearest explanation of what went wrong.
            Log::error('Fast2SMS: HTTP error response.', [
                'phone'  => $this->maskPhone($phoneNumber),
                'status' => $response->status(),
                'body'   => Str::limit($response->body(), 1000),
            ]);

            return false;
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['return'] ?? false) !== true) {
            // On failure Fast2SMS returns a "message"/"status_code" describing
            // *why* (e.g. invalid key, low wallet balance, DND number) — safe
            // to log, it is never the OTP or the API key.
            Log::error('Fast2SMS: API reported failure.', [
                'phone'        => $this->maskPhone($phoneNumber),
                'http_status'  => $response->status(),
                'return'       => $payload['return'] ?? null,
                'status_code'  => $payload['status_code'] ?? null,
                'reason'       => $payload['message'] ?? null,
                'request_id'   => $payload['request_id'] ?? null,
                'raw_response' => is_array($payload) ? null : $response->body(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Mask a phone number for safe logging, e.g. 98765XXXXX.
     */
    private function maskPhone(string $phoneNumber): string
    {
        return strlen($phoneNumber) > 5
            ? substr($phoneNumber, 0, 5) . str_repeat('X', strlen($phoneNumber) - 5)
            : 'XXXXX';
    }
}
