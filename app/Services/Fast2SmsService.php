<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin wrapper around the Fast2SMS Quick SMS API (route=q).
 *
 * Docs: https://docs.fast2sms.com/reference/quick-sms-post
 *   POST https://www.fast2sms.com/dev/bulkV2
 *   Header: Authorization: <api key>
 *   Body:   route=q, message=<text>, numbers=<comma separated 10-digit numbers>
 *
 * The API key lives only in Laravel config (config('services.fast2sms.api_key'),
 * sourced from FAST2SMS_API_KEY in .env) and is never returned to callers or logged.
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
        $apiKey = config('services.fast2sms.api_key');

        if (blank($apiKey)) {
            Log::error('Fast2SMS: API key is not configured (FAST2SMS_API_KEY).');

            return false;
        }

        try {
            $response = Http::withHeaders([
                    'authorization' => $apiKey,
                ])
                ->asForm()
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(self::ENDPOINT, [
                    'route'    => 'q',
                    'message'  => $message,
                    'numbers'  => $phoneNumber,
                    'language' => 'english',
                    'flash'    => 0,
                ]);
        } catch (Throwable $e) {
            Log::error('Fast2SMS: request failed.', [
                'phone' => $this->maskPhone($phoneNumber),
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        if ($response->failed()) {
            Log::error('Fast2SMS: HTTP error response.', [
                'phone'  => $this->maskPhone($phoneNumber),
                'status' => $response->status(),
            ]);

            return false;
        }

        $payload = $response->json();

        if (! is_array($payload) || ($payload['return'] ?? false) !== true) {
            Log::error('Fast2SMS: API reported failure.', [
                'phone'      => $this->maskPhone($phoneNumber),
                'status'     => $response->status(),
                'return'     => $payload['return'] ?? null,
                'request_id' => $payload['request_id'] ?? null,
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
