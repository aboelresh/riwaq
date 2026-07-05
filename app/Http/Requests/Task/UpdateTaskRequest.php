<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => 'sometimes|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'status'        => 'sometimes|in:' . TaskStatus::validationValues(),
            'priority'      => 'sometimes|in:' . TaskPriority::validationValues(),
            'label'         => 'nullable|string|max:50',
            'section_id'    => 'nullable|integer|exists:team_sections,id',
            'assignee_ids'  => 'nullable|array',
            'assignee_ids.*'=> 'integer|exists:users,id',
            'due_date'      => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'   => 'Status must be: ' . TaskStatus::validationValues(),
            'priority.in' => 'Priority must be: ' . TaskPriority::validationValues(),
        ];
    }
}