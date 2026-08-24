<?php

namespace App\Http\Requests\Quiz;

use App\Models\Quiz;
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
            'answers'               => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer_id'   => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'answers.required'               => 'Answers are required.',
            'answers.array'                  => 'Answers must be an array.',
            'answers.min'                    => 'At least one answer is required.',
            'answers.*.question_id.required' => 'Each answer must have a question_id.',
            'answers.*.answer_id.required'   => 'Each answer must have an answer_id.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $quizId = $this->route('id');
            $quiz   = Quiz::with('questions.answers')->find($quizId);

            if (!$quiz) return;

            $validQuestionIds = $quiz->questions->pluck('id')->toArray();

            foreach ($this->answers ?? [] as $index => $answer) {
                $questionId = $answer['question_id'] ?? null;
                $answerId   = $answer['answer_id'] ?? null;

                if (!in_array($questionId, $validQuestionIds)) {
                    $validator->errors()->add(
                        "answers.{$index}.question_id",
                        "Question ID {$questionId} does not belong to this quiz."
                    );
                    continue;
                }

                $question      = $quiz->questions->find($questionId);
                $validAnswerIds = $question->answers->pluck('id')->toArray();

                if (!in_array($answerId, $validAnswerIds)) {
                    $validator->errors()->add(
                        "answers.{$index}.answer_id",
                        "Answer ID {$answerId} does not belong to question {$questionId}."
                    );
                }
            }
        });
    }
}