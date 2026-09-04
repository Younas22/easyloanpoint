<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by OtpService whenever an OTP-related request cannot proceed
 * (cooldown, expiry, wrong code, attempt limit, invalid/expired token, …).
 *
 * Carries an HTTP status code and optional safe metadata so the controller
 * can translate it directly into a JSON response without leaking internals.
 */
class OtpVerificationException extends RuntimeException
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        string $message,
        public readonly int $statusCode = 422,
        public readonly array $meta = [],
    ) {
        parent::__construct($message);
    }

    public static function cooldownActive(int $retryAfterSeconds): self
    {
        return new self(
            'Please wait before requesting another OTP.',
            429,
            ['retry_after_seconds' => $retryAfterSeconds],
        );
    }

    public static function alreadyRegistered(): self
    {
        return new self('This mobile number is already registered.', 409);
    }

    public static function notFound(): self
    {
        return new self('No active OTP request found for this mobile number. Please request a new OTP.', 422);
    }

    public static function expired(): self
    {
        return new self('OTP has expired. Please request a new one.', 422);
    }

    public static function maxAttemptsReached(): self
    {
        return new self('Maximum OTP attempts exceeded. Please request a new OTP.', 429);
    }

    public static function invalidCode(int $attemptsRemaining): self
    {
        return new self(
            'Incorrect OTP.',
            422,
            ['attempts_remaining' => $attemptsRemaining],
        );
    }

    public static function invalidOrExpiredToken(): self
    {
        return new self('Verification token is invalid or has expired. Please verify your mobile number again.', 422);
    }

    public static function tokenPhoneMismatch(): self
    {
        return new self('Verification token does not match the mobile number being registered.', 422);
    }

    public static function sendFailed(): self
    {
        return new self('Failed to send OTP. Please try again in a moment.', 502);
    }
}
