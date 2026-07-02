<?php

namespace App\Services;

use App\Models\User;
use App\Models\Course;
use App\Models\Topic;
use App\Models\Track;
use App\Models\UserCourseProgress;
use App\Models\UserTopicProgress;
use App\Models\UserQuizAttempt;
use Illuminate\Support\Facades\DB;

class ProgressService
{
    public function initializeTrackProgress(User $user, Track $track): void
    {
        DB::transaction(function () use ($user, $track) {
            $courses = $track->courses()->orderBy('order')->get();

            foreach ($courses as $course) {
                $this->initializeCourseProgress($user, $course);

                $topics = $course->topics()->orderBy('order')->get();

                foreach ($topics as $topic) {
                    $this->initializeTopicProgress($user, $topic);
                }
            }
        });
    }

    public function initializeCourseProgress(User $user, Course $course): UserCourseProgress
    {
        return UserCourseProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'is_unlocked' => false,
                'total_score' => 0,
                'max_possible_score' => $this->calculateCourseMaxScore($course),
                'is_completed' => false,
            ]
        );
    }

    public function initializeTopicProgress(User $user, Topic $topic): UserTopicProgress
    {
        return UserTopicProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'topic_id' => $topic->id,
            ],
            [
                'is_unlocked' => false,
                'is_viewed' => false,
            ]
        );
    }

    public function markTopicAsViewed(User $user, Topic $topic): void
    {
        $progress = $this->initializeTopicProgress($user, $topic);
        
        if (!$progress->is_viewed) {
            $progress->update([
                'is_viewed' => true,
                'viewed_at' => now(),
            ]);
        }
    }

    public function updateCourseScore(User $user, Course $course): void
    {
        $progress = $this->initializeCourseProgress($user, $course);
        
        $topicQuizScores = UserQuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz', function ($query) use ($course) {
                $query->where('type', 'topic')
                      ->whereHas('topic.courses', function ($q) use ($course) {
                          $q->where('courses.id', $course->id);
                      });
            })
            ->where('passed', true)
            ->sum('score');
        
        $courseQuizScore = UserQuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz', function ($query) use ($course) {
                $query->where('type', 'course')
                      ->where('course_id', $course->id);
            })
            ->where('passed', true)
            ->sum('score');
        
        $totalScore = $topicQuizScores + $courseQuizScore;
        
        $progress->update([
            'total_score' => $totalScore,
        ]);
    }

    public function checkCourseCompletion(User $user, Course $course): bool
    {
        $progress = $this->initializeCourseProgress($user, $course);
        
        $hasPassedCourseQuiz = UserQuizAttempt::where('user_id', $user->id)
            ->whereHas('quiz', function ($query) use ($course) {
                $query->where('type', 'course')
                      ->where('course_id', $course->id);
            })
            ->where('passed', true)
            ->exists();
        
        if (!$hasPassedCourseQuiz) {
            return false;
        }
        
        $this->updateCourseScore($user, $course);
        $progress->refresh();
        
        $isPassed = $progress->hasPassed();
        
        if ($isPassed && !$progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
        
        return $isPassed;
    }

    private function calculateCourseMaxScore(Course $course): int
    {
        $topicQuizPoints = $course->topics()
            ->with('quiz')
            ->get()
            ->sum(fn($topic) => $topic->quiz?->total_points ?? 0);
        
        $courseQuizPoints = $course->quizzes()
            ->where('type', 'course')
            ->sum('total_points');
        
        return $topicQuizPoints + $courseQuizPoints;
    }

    public function getUserProgressSummary(User $user)
    {
        return [
            'total_tracks' => $user->tracks()->count(),
            'total_courses_unlocked' => $user->courseProgress()->where('is_unlocked', true)->count(),
            'total_courses_completed' => $user->courseProgress()->where('is_completed', true)->count(),
            'total_topics_viewed' => $user->topicProgress()->where('is_viewed', true)->count(),
            'total_quizzes_passed' => $user->quizAttempts()->where('passed', true)->count(),
            'total_score' => $user->quizAttempts()->where('passed', true)->sum('score'),
        ];
    }
}