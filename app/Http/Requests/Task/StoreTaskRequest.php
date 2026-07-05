<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'status'        => 'nullable|in:' . TaskStatus::validationValues(),
            'priority'      => 'nullable|in:' . TaskPriority::validationValues(),
            'label'         => 'nullable|string|max:50',
            'section_id'    => 'nullable|integer|exists:team_sections,id',
            'assignee_ids'  => 'nullable|array',
            'assignee_ids.*'=> 'integer|exists:users,id',
            'due_date'      => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Task title is required.',
            'status.in'         => 'Status must be: ' . TaskStatus::validationValues(),
            'priority.in'       => 'Priority must be: ' . TaskPriority::validationValues(),
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}