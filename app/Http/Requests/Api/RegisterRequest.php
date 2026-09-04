<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'min:2', 'max:100'],
            'mobile' => [
                'required', 'string', 'regex:/^[6-9]\d{9}$/',
                // Only a completed (phone-verified) account blocks re-registration.
                // A stale, never-verified row for the same number does not.
                Rule::unique('users', 'phone')->where(fn ($q) => $q->whereNotNull('phone_verified_at')),
            ],
            'email' => [
                'nullable', 'email', 'max:191',
                Rule::unique('users', 'email')->where(fn ($q) => $q->whereNotNull('phone_verified_at')),
            ],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'verification_token' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex'               => 'Enter a valid 10-digit Indian mobile number.',
            'mobile.unique'               => 'This mobile number is already registered.',
            'email.unique'               => 'This email address is already registered.',
            'verification_token.required' => 'Mobile number verification is required before registration.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
