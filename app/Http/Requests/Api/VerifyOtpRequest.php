<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mobile'  => ['required', 'string', 'regex:/^[6-9]\d{9}$/'],
            'otp'     => ['required', 'string', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.digits'   => 'OTP must be exactly 6 digits.',
            'mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
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
