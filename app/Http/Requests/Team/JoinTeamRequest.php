<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class JoinTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'     => 'required|string|exists:teams,code',
            'track_id' => 'required|integer|exists:tracks,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'     => 'Team code is required.',
            'code.exists'       => 'Invalid team code. No team found with this code.',
            'track_id.required' => 'Track is required.',
            'track_id.exists'   => 'The selected track does not exist.',
        ];
    }
}