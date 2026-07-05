<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'    => 'required|integer|exists:users,id',
            'content'    => 'required|string|max:2000',
            'is_private' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'    => 'User ID is required.',
            'user_id.exists'      => 'The selected user does not exist.',
            'content.required'    => 'Note content is required.',
            'content.max'         => 'Note must not exceed 2000 characters.',
            'is_private.required' => 'is_private field is required.',
            'is_private.boolean'  => 'is_private must be true or false.',
        ];
    }
}