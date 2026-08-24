<?php

namespace App\Events;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TopicCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User  $user,
        public Topic $topic
    ) {}
}