<?php

namespace App\Events;

use App\Models\Track;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User  $user,
        public Track $track
    ) {}
}