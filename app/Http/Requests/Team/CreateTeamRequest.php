<?php

namespace App\Http\Requests\Team;

use App\Enums\ProjectType;
use App\Enums\TeamType;
use Illuminate\Foundation\Http\FormRequest;

class CreateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'type'         => 'required|in:' . TeamType::validationValues(),
            'project_type' => 'required|in:' . ProjectType::validationValues(),
            'max_members'  => 'required|integer|min:2|max:10',
            'track_id'     => 'required|integer|exists:tracks,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Team name is required.',
            'type.required'         => 'Team type is required.',
            'type.in'               => 'Team type must be: ' . TeamType::validationValues(),
            'project_type.required' => 'Project type is required.',
            'project_type.in'       => 'Project type must be: ' . ProjectType::validationValues(),
            'max_members.min'       => 'Team must allow at least 2 members.',
            'max_members.max'       => 'Team cannot exceed 10 members.',
            'track_id.required'     => 'Track is required.',
            'track_id.exists'       => 'The selected track does not exist.',
        ];
    }
}