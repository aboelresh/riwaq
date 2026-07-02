<?php

namespace App\Services;

use App\Models\User;
use App\Models\Track;
use App\Models\Course;
use App\Models\Topic;
use App\Models\UserCourseProgress;
use App\Models\UserTopicProgress;

class UnlockService
{
    public function __construct(
        private ProgressService $progressService,
        private QuizService $quizService
    ) {}

    public function unlockFirstCourseInTrack(User $user, Track $track): void
    {
        $firstCourse = $track->courses()->orderBy('order')->first();
        
        if (!$firstCourse) {
            return;
        }
        
        $progress = $this->progressService->initializeCourseProgress($user, $firstCourse);
        
        if (!$progress->is_unlocked) {
            $progress->update([
                'is_unlocked' => true,
                'started_at' => now(),
            ]);
            
            $this->unlockFirstTopicInCourse($user, $firstCourse);
        }
    }

    public function unlockFirstTopicInCourse(User $user, Course $course): void
    {
        $firstTopic = $course->topics()->orderBy('order')->first();
        
        if (!$firstTopic) {
            return;
        }
        
        $progress = $this->progressService->initializeTopicProgress($user, $firstTopic);
        
        if (!$progress->is_unlocked) {
            $progress->update(['is_unlocked' => true]);
        }
    }

    public function unlockNextTopicInCourse(User $user, Course $course, Topic $currentTopic): ?Topic
    {
        $topics = $course->topics()->orderBy('order')->get();
        $currentIndex = $topics->search(fn($t) => $t->id === $currentTopic->id);
        
        if ($currentIndex === false || $currentIndex >= $topics->count() - 1) {
            return null;
        }
        
        $nextTopic = $topics[$currentIndex + 1];
        
        $topicQuiz = $currentTopic->quiz;
        
        if (!$topicQuiz) {
            return null;
        }
        
        $lastAttempt = $this->quizService->getLastAttempt($user, $topicQuiz);
        
        if (!$lastAttempt || !$lastAttempt->passed) {
            return null;
        }
        
        $progress = $this->progressService->initializeTopicProgress($user, $nextTopic);
        
        if (!$progress->is_unlocked) {
            $progress->update(['is_unlocked' => true]);
        }
        
        return $nextTopic;
    }

    public function unlockNextCourseInTrack(User $user, Track $track, Course $currentCourse): ?Course
    {
        $courseCompleted = $this->progressService->checkCourseCompletion($user, $currentCourse);
        
        if (!$courseCompleted) {
            return null;
        }
        
        $courses = $track->courses()->orderBy('order')->get();
        $currentIndex = $courses->search(fn($c) => $c->id === $currentCourse->id);
        
        if ($currentIndex === false || $currentIndex >= $courses->count() - 1) {
            return null;
        }
        
        $nextCourse = $courses[$currentIndex + 1];
        
        $progress = $this->progressService->initializeCourseProgress($user, $nextCourse);
        
        if (!$progress->is_unlocked) {
            $progress->update([
                'is_unlocked' => true,
                'started_at' => now(),
            ]);
            
            $this->unlockFirstTopicInCourse($user, $nextCourse);
        }
        
        return $nextCourse;
    }

    public function isTopicUnlocked(User $user, Topic $topic): bool
    {
        $progress = UserTopicProgress::where('user_id', $user->id)
            ->where('topic_id', $topic->id)
            ->first();
        
        return $progress?->is_unlocked ?? false;
    }

    public function isCourseUnlocked(User $user, Course $course): bool
    {
        $progress = UserCourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
        
        return $progress?->is_unlocked ?? false;
    }
}