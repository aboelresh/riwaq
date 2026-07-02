<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    public function view(User $user, Topic $topic): bool
    {
        // Check if topic is unlocked for user
        $progress = $user->topicProgress()
            ->where('topic_id', $topic->id)
            ->first();

        return $progress && $progress->is_unlocked;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Topic $topic): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Topic $topic): bool
    {
        return $user->isAdmin();
    }

    public function markAsViewed(User $user, Topic $topic): bool
    {
        return $this->view($user, $topic);
    }
}