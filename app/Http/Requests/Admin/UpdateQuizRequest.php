<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                              => 'required|string|max:255',
            'type'                               => 'required|in:assessment,topic,course',
            'topic_id'                           => 'required_if:type,topic|nullable|exists:topics,id',
            'course_id'                          => 'required_if:type,course|nullable|exists:courses,id',
            'total_points'                       => 'required|integer|min:1',
            'pass_percentage'                    => 'required|integer|min:0|max:100',
            'questions'                          => 'nullable|array',
            'questions.*.question_text'          => 'required_with:questions|string',
            'questions.*.points'                 => 'required_with:questions|integer|min:1',
            'questions.*.answers'                => 'required_with:questions|array|min:2',
            'questions.*.answers.*.answer_text'  => 'required|string',
            'questions.*.answers.*.is_correct'   => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Quiz title is required.',
            'type.in'           => 'Quiz type must be: assessment, topic, or course.',
            'questions.*.answers.min' => 'Each question must have at least 2 answers.',
        ];
    }

    // Bug 015 Fix applies to updates too
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->questions ?? [] as $index => $question) {
                $answers    = $question['answers'] ?? [];
                $hasCorrect = collect($answers)->contains('is_correct', true);

                if (!empty($answers) && !$hasCorrect) {
                    $validator->errors()->add(
                        "questions.{$index}.answers",
                        "Question " . ($index + 1) . " must have at least one correct answer."
                    );
                }
            }
        });
    }
}