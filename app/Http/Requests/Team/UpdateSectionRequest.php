<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:50',
        ];
    }
}