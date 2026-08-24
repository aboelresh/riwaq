<?php

namespace App\Listeners;

use App\Services\NotificationService;

class SendMilestoneNotification
{
   
    public function handleTopicCompleted(\App\Events\TopicCompleted $event): void
    {
        NotificationService::send(
            userId: $event->user->id,
            type:   'topic_completed',
            title:  '👏🤍Topic Completed!',
            body:   "You completed: {$event->topic->title}",
            data:   ['topic_id' => $event->topic->id]
        );
    }

    /**
     * Handle CourseCompleted event.
     */
    public function handleCourseCompleted(\App\Events\CourseCompleted $event): void
    {
        NotificationService::send(
            userId: $event->user->id,
            type:   'course_completed',
            title:  '🤍👏 Course Completed!',
            body:   "You finished: {$event->course->title}",
            data:   ['course_id' => $event->course->id]
        );
    }

    /**
     * Handle TrackCompleted event.
     */
    public function handleTrackCompleted(\App\Events\TrackCompleted $event): void
    {
        NotificationService::send(
            userId: $event->user->id,
            type:   'track_completed',
            title:  '👏🤍 Track Completed!',
            body:   "You mastered the entire track: {$event->track->title}",
            data:   ['track_id' => $event->track->id]
        );
    }
}