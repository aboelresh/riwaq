<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class InviteByUsernameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|exists:users,username',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.exists'   => 'No user found with this username.',
        ];
    }
}