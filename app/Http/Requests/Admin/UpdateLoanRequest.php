<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'loan_type'        => ['required', 'in:personal,home,business,vehicle,education'],
            'amount_requested' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'amount_approved'  => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'interest_rate'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tenure_months'    => ['nullable', 'integer', 'min:1', 'max:360'],
            'repayment_days'   => ['nullable', 'integer', 'min:1', 'max:3650'],
            'purpose'          => ['nullable', 'string', 'max:500'],
            'assigned_hr_id'   => ['nullable', 'exists:users,id'],
            'remarks'          => ['nullable', 'string', 'max:1000'],
        ];
    }
}
