<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.max'      => 'Comment must not exceed 1000 characters.',
        ];
    }
}