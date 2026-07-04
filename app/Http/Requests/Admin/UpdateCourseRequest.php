<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'topic_ids'   => 'nullable|array',
            'topic_ids.*' => 'integer|exists:topics,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'Course title is required.',
            'topic_ids.array'    => 'topic_ids must be an array.',
            'topic_ids.*.exists' => 'One or more topic IDs do not exist.',
        ];
    }
}