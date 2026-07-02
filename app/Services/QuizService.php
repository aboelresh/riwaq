<?php

namespace App\Services;

use App\Models\User;
use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use App\Models\UserQuizAnswer;
use Carbon\Carbon;

class QuizService
{
    public function submitQuiz(User $user, Quiz $quiz, array $answers): UserQuizAttempt
    {
        
        $lastAttempt = $this->getLastAttempt($user, $quiz);
        
        if ($lastAttempt && !$lastAttempt->canRetryNow()) {
            throw new \Exception('You must wait before retrying this quiz.');
        }
        
        $score = 0;
        $maxScore = 0;
        $userAnswers = [];
        
        foreach ($quiz->questions as $question) {
            $maxScore += $question->points;
            
            $answerId = $answers[$question->id] ?? null;
            
            if (!$answerId) {
                continue;
            }
            
            $answer = $question->answers()->find($answerId);
            
            if (!$answer) {
                continue;
            }
            
            $isCorrect = $answer->is_correct;
            
            if ($isCorrect) {
                $score += $question->points;
            }
            
            $userAnswers[] = [
                'question_id' => $question->id,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
            ];
        }
        
        $passPercentage = $quiz->pass_percentage;
        $achievedPercentage = ($maxScore > 0) ? ($score / $maxScore) * 100 : 0;
        $passed = $achievedPercentage >= $passPercentage;
        
        // Before creating attempt, get attempt number
$attemptNumber = UserQuizAttempt::where('user_id', $user->id)
    ->where('quiz_id', $quiz->id)
    ->count() + 1;

// Create attempt with attempt number
$attempt = UserQuizAttempt::create([
    'user_id' => $user->id,
    'quiz_id' => $quiz->id,
    'attempt_number' => $attemptNumber,  // ← Add this
    'score' => $score,
    'max_score' => $maxScore,
    'passed' => $passed,
    'can_retry_at' => !$passed ? Carbon::now()->addHour() : null,
    'attempted_at' => now(),
]);
        
        foreach ($userAnswers as $userAnswer) {
            UserQuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $userAnswer['question_id'],
                'answer_id' => $userAnswer['answer_id'],
                'is_correct' => $userAnswer['is_correct'],
            ]);
        }
        
        return $attempt;
    }

    public function getLastAttempt(User $user, Quiz $quiz): ?UserQuizAttempt
    {
        return UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->latest('attempted_at')
            ->first();
    }

    public function canTakeQuiz(User $user, Quiz $quiz): bool
    {
        $lastAttempt = $this->getLastAttempt($user, $quiz);
        
        if (!$lastAttempt) {
            return true;
        }
        
        if ($lastAttempt->passed) {
            return false;
        }
        
        return $lastAttempt->canRetryNow();
    }

    public function getBestAttempt(User $user, Quiz $quiz): ?UserQuizAttempt
    {
        return UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('score', 'desc')
            ->first();
    }

    public function getAllAttempts(User $user, Quiz $quiz)
    {
        return UserQuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('attempted_at', 'desc')
            ->get();
    }
}