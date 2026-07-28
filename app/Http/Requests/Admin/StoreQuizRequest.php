<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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
            'questions'                          => 'required|array|min:1',
            'questions.*.question_text'          => 'required|string',
            'questions.*.points'                 => 'required|integer|min:1',
            'questions.*.answers'                => 'required|array|min:2',
            'questions.*.answers.*.answer_text'  => 'required|string',
            'questions.*.answers.*.is_correct'   => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'                             => 'Quiz title is required.',
            'type.in'                                    => 'Quiz type must be: assessment, topic, or course.',
            'topic_id.required_if'                       => 'topic_id is required when type is topic.',
            'course_id.required_if'                      => 'course_id is required when type is course.',
            'questions.required'                         => 'At least one question is required.',
            'questions.*.question_text.required'         => 'Each question must have question_text.',
            'questions.*.answers.min'                    => 'Each question must have at least 2 answers.',
            'questions.*.answers.*.answer_text.required' => 'Each answer must have answer_text.',
            'questions.*.answers.*.is_correct.required'  => 'Each answer must have is_correct (true/false).',
        ];
    }

    /**
     * Bug 015 Fix: ensure every question has at least one correct answer.
     * The rules() above validate structure (min:2 answers, is_correct is boolean)
     * but can't enforce that at least ONE answer per question is true.
     * A quiz with zero correct answers cannot be passed — it's an invalid state.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->questions ?? [] as $index => $question) {
                $answers    = $question['answers'] ?? [];
                $hasCorrect = collect($answers)->contains('is_correct', true);

                if (!$hasCorrect) {
                    $validator->errors()->add(
                        "questions.{$index}.answers",
                        "Question " . ($index + 1) . " must have at least one correct answer (is_correct: true)."
                    );
                }
            }
        });
    }
}