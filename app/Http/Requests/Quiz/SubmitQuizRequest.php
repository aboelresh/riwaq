<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class SubmitQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answers'              => 'required|array|min:1',
            'answers.*.question_id'=> 'required|integer',
            'answers.*.answer_id'  => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required'               => 'Answers are required.',
            'answers.array'                  => 'Answers must be an array.',
            'answers.min'                    => 'At least one answer is required.',
            'answers.*.question_id.required' => 'Each answer must have a question_id.',
            'answers.*.question_id.integer'  => 'question_id must be an integer.',
            'answers.*.answer_id.required'   => 'Each answer must have an answer_id.',
            'answers.*.answer_id.integer'    => 'answer_id must be an integer.',
        ];
    }
}