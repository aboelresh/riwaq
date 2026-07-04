<?php

namespace App\Http\Requests\Assessment;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers'   => 'required|array|min:1',
            'answers.*' => 'required|string|in:A,B,C,D',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required'   => 'Answers are required.',
            'answers.array'      => 'Answers must be an array.',
            'answers.min'        => 'At least one answer is required.',
            'answers.*.required' => 'Each answer is required.',
            'answers.*.in'       => 'Each answer must be one of: A, B, C, D (uppercase only).',
        ];
    }
}