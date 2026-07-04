<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'course_ids'   => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'      => 'Track title is required.',
            'course_ids.array'    => 'course_ids must be an array of course IDs.',
            'course_ids.*.exists' => 'One or more course IDs do not exist.',
        ];
    }
}