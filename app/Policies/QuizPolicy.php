<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use App\Services\QuizService;

class QuizPolicy
{
    public function __construct(
        private QuizService $quizService
    ) {}

    public function view(User $user, Quiz $quiz): bool
    {
        // For topic quizzes, check if topic is unlocked
        if ($quiz->type === 'topic' && $quiz->topic_id) {
            $topic = $quiz->topic;
            if (!$topic) return false;

            $progress = $user->topicProgress()
                ->where('topic_id', $topic->id)
                ->first();

            return $progress && $progress->is_unlocked;
        }

        // For course quizzes, check if all topics are viewed
        if ($quiz->type === 'course' && $quiz->course_id) {
            $course = $quiz->course;
            if (!$course) return false;

            $topics = $course->topics;
            foreach ($topics as $topic) {
                $progress = $user->topicProgress()
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!$progress || !$progress->is_viewed) {
                    return false;
                }
            }

            return true;
        }

        // Assessment quizzes are always available
        return true;
    }

    public function submit(User $user, Quiz $quiz): bool
    {
        if (!$this->view($user, $quiz)) {
            return false;
        }

        return $this->quizService->canTakeQuiz($user, $quiz);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Quiz $quiz): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        return $user->isAdmin();
    }
}