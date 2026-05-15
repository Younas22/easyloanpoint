<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'name'            => ['required', 'string', 'max:150'],
            'phone'           => ['required', 'digits:10', Rule::unique('customers', 'phone')->ignore($customerId)],
            'email'           => ['nullable', 'email', 'max:150', Rule::unique('customers', 'email')->ignore($customerId)],
            'aadhaar_number'  => ['nullable', 'digits:12', Rule::unique('customers', 'aadhaar_number')->ignore($customerId)],
            'pan_number'      => ['nullable', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/','max:10', Rule::unique('customers', 'pan_number')->ignore($customerId)],
            'dob'             => ['nullable', 'date', 'before:today'],
            'gender'          => ['nullable', Rule::in(['male', 'female', 'other'])],
            'address'         => ['nullable', 'string', 'max:500'],
            'city'            => ['nullable', 'string', 'max:100'],
            'state'           => ['nullable', 'string', 'max:100'],
            'pincode'         => ['nullable', 'digits:6'],
            'employment_type' => ['nullable', Rule::in(['salaried', 'self_employed', 'business', 'unemployed'])],
            'salary'          => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'status'          => ['required', Rule::in(['active', 'inactive', 'blacklisted'])],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Customer name is required.',
            'phone.required'          => 'Mobile number is required.',
            'phone.digits'            => 'Mobile number must be exactly 10 digits.',
            'phone.unique'            => 'This mobile number is already registered.',
            'email.unique'            => 'This email address is already registered.',
            'aadhaar_number.digits'   => 'Aadhaar number must be exactly 12 digits.',
            'aadhaar_number.unique'   => 'This Aadhaar number is already registered.',
            'pan_number.regex'        => 'PAN must be in format: AAAAA9999A (5 letters, 4 digits, 1 letter).',
            'pan_number.unique'       => 'This PAN number is already registered.',
            'dob.before'              => 'Date of birth must be a past date.',
            'pincode.digits'          => 'Pincode must be exactly 6 digits.',
            'salary.numeric'          => 'Salary must be a valid number.',
        ];
    }
}
