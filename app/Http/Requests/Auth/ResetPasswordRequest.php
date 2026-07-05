<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'code'     => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => 'Email is required.',
            'code.required'      => 'Reset code is required.',
            'code.size'          => 'Reset code must be exactly 6 digits.',
            'password.required'  => 'New password is required.',
            'password.min'       => 'New password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}