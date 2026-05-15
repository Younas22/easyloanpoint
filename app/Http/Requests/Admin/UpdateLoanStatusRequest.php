<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status'          => ['required', 'in:pending,under_review,approved,rejected,disbursed'],
            'remarks'         => ['nullable', 'string', 'max:1000'],
            'amount_approved' => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'interest_rate'   => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
