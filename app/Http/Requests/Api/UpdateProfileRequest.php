<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'            => ['sometimes', 'string', 'min:2', 'max:100'],
            'email'           => ['sometimes', 'nullable', 'email', "unique:users,email,{$userId}"],
            'profile_image'   => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'aadhaar_number'  => ['sometimes', 'digits:12'],
            'pan_number'      => ['sometimes', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'address'         => ['sometimes', 'string', 'max:500'],
            'city'            => ['sometimes', 'string', 'max:100'],
            'state'           => ['sometimes', 'string', 'max:100'],
            'pincode'         => ['sometimes', 'digits:6'],
            'dob'             => ['sometimes', 'date', 'before:today'],
            'gender'          => ['sometimes', 'in:male,female,other'],
            'employment_type' => ['sometimes', 'in:salaried,self_employed,business,unemployed'],
            'salary'          => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'aadhaar_number.digits'  => 'Aadhaar number must be exactly 12 digits.',
            'pan_number.regex'       => 'PAN number must be in format: ABCDE1234F.',
            'pincode.digits'         => 'Pincode must be exactly 6 digits.',
            'dob.before'             => 'Date of birth must be in the past.',
            'profile_image.max'      => 'Profile image must not exceed 2MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
