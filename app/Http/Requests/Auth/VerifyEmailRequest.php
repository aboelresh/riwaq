<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|size:6',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Verification code is required.',
            'code.size'     => 'Verification code must be exactly 6 digits.',
        ];
    }
}