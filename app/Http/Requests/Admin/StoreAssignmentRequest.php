<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'hr_id'       => ['required', 'integer', 'exists:users,id'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->hr_id) {
                $hr = \App\Models\User::find($this->hr_id);
                if (! $hr || $hr->role !== 'hr') {
                    $v->errors()->add('hr_id', 'The selected user is not an HR executive.');
                }
                if ($hr && ! $hr->status) {
                    $v->errors()->add('hr_id', 'The selected HR executive is inactive.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists'   => 'Selected customer does not exist.',
            'hr_id.required'       => 'Please select an HR executive.',
            'hr_id.exists'         => 'Selected HR executive does not exist.',
        ];
    }
}
