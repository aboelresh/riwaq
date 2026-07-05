<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Section name is required.',
            'name.max'      => 'Section name must not exceed 100 characters.',
        ];
    }
}