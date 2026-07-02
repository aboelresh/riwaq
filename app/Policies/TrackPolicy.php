<?php

namespace App\Policies;

use App\Models\Track;
use App\Models\User;

class TrackPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Track $track): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Track $track): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Track $track): bool
    {
        return $user->isAdmin();
    }

    public function enroll(User $user, Track $track): bool
    {
        return !$user->tracks()->where('track_id', $track->id)->exists();
    }
}