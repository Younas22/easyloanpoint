<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hrId = $this->route('hr');

        return [
            'name'          => ['required', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($hrId)],
            'phone'         => ['required', 'digits:10'],
            'password'      => $hrId ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
            'status'        => ['required', 'boolean'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'HR name is required.',
            'email.required'         => 'Email address is required.',
            'email.unique'           => 'This email is already registered.',
            'phone.required'         => 'Phone number is required.',
            'phone.digits'           => 'Phone number must be exactly 10 digits.',
            'password.required'      => 'Password is required.',
            'password.min'           => 'Password must be at least 8 characters.',
            'password.confirmed'     => 'Password confirmation does not match.',
            'profile_image.image'    => 'Profile image must be a valid image file.',
            'profile_image.mimes'    => 'Profile image must be JPG, JPEG, or PNG.',
            'profile_image.max'      => 'Profile image must not exceed 2MB.',
        ];
    }
}
