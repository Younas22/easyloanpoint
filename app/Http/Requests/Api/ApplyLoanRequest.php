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
            'loan_type'        => ['required', 'string', 'max:100'],
            'loan_type_id'     => ['nullable', 'integer', 'exists:loan_types,id'],
            'amount_requested' => ['required', 'numeric', 'min:100', 'max:10000000'],
            'repayment_days'   => ['nullable', 'integer', 'min:1'],
            'tenure_months'    => ['nullable', 'integer', 'min:1', 'max:360'],
            'purpose'          => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'loan_type.required'         => 'Loan type is required.',
            'amount_requested.min'       => 'Minimum loan amount is ₹100.',
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
