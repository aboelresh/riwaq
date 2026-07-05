<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_members' => 'sometimes|integer|min:2|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max'         => 'Team name must not exceed 255 characters.',
            'max_members.min'  => 'Team must allow at least 2 members.',
            'max_members.max'  => 'Team cannot exceed 10 members.',
        ];
    }
}