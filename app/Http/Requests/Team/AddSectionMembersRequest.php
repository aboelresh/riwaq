<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class AddSectionMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'required|integer|exists:users,id',
            'role'       => 'nullable|string|in:lead,member',
        ];
    }

    public function messages(): array
    {
        return [
            'user_ids.required'   => 'At least one user ID is required.',
            'user_ids.*.exists'   => 'One or more user IDs do not exist.',
            'role.in'             => 'Role must be: lead or member.',
        ];
    }
}