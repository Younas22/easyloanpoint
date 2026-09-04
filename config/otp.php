<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    |
    | Controls how mobile OTP verification behaves across the app: length of
    | the generated code, how long it stays valid, how often a phone number
    | may request a new code, and how many wrong guesses are tolerated before
    | the code is locked out. All values are configurable via .env so they
    | can be tuned per environment without touching code.
    |
    */

    'length' => (int) env('OTP_LENGTH', 6),

    'expiry_minutes' => (int) env('OTP_EXPIRY_MINUTES', 5),

    'resend_cooldown_seconds' => (int) env('OTP_RESEND_COOLDOWN_SECONDS', 60),

    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Verification Token
    |--------------------------------------------------------------------------
    |
    | Once an OTP is verified, a short-lived, single-use verification token
    | is issued. The final registration request must present this token as
    | proof that the exact phone number was OTP-verified. It is never the
    | OTP itself.
    |
    */

    'verification_token_ttl_minutes' => (int) env('OTP_VERIFICATION_TOKEN_TTL_MINUTES', 15),

];
