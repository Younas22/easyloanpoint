<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'mobile'   => ['required', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:users,phone'],
            'email'    => ['nullable', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.regex'  => 'Enter a valid 10-digit Indian mobile number.',
            'mobile.unique' => 'This mobile number is already registered.',
            'email.unique'  => 'This email address is already registered.',
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
