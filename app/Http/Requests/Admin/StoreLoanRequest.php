<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'customer_id'      => ['required', 'exists:customers,id'],
            'loan_type'        => ['required', 'in:personal,home,business,vehicle,education'],
            'amount_requested' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'tenure_months'    => ['required', 'integer', 'min:1', 'max:360'],
            'purpose'          => ['nullable', 'string', 'max:500'],
            'assigned_hr_id'   => ['nullable', 'exists:users,id'],
            'remarks'          => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_requested.min' => 'Minimum loan amount is ₹1,000.',
            'amount_requested.max' => 'Maximum loan amount is ₹1,00,00,000.',
            'tenure_months.max'    => 'Maximum tenure is 360 months (30 years).',
        ];
    }
}
