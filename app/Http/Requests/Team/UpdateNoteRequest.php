<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'    => 'required|string|max:2000',
            'is_private' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Note content is required.',
            'content.max'      => 'Note must not exceed 2000 characters.',
        ];
    }
}