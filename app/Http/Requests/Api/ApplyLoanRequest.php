<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApplyLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_type'        => ['required', 'in:personal,home,business,vehicle,education'],
            'amount_requested' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'tenure_months'    => ['required', 'integer', 'min:1', 'max:360'],
            'purpose'          => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_type.in'               => 'Loan type must be one of: personal, home, business, vehicle, education.',
            'amount_requested.min'       => 'Minimum loan amount is ₹1,000.',
            'amount_requested.max'       => 'Maximum loan amount is ₹1,00,00,000.',
            'tenure_months.min'          => 'Minimum tenure is 1 month.',
            'tenure_months.max'          => 'Maximum tenure is 360 months (30 years).',
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
