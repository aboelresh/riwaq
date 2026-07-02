<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Team chat — presence channel (all team members)
Broadcast::channel('chat.team.{teamId}', function ($user, $teamId) {
    $isMember = \DB::table('team_members')->where('team_id', $teamId)->where('user_id', $user->id)->exists();
    return $isMember || $user->role === 'admin'
        ? ['id' => $user->id, 'name' => $user->name, 'profile_photo' => $user->profile_photo]
        : false;
});

// Section chat — presence channel (section members)
Broadcast::channel('chat.section.{sectionId}', function ($user, $sectionId) {
    $isMember = \DB::table('team_section_members')->where('section_id', $sectionId)->where('user_id', $user->id)->exists();
    return $isMember || $user->role === 'admin'
        ? ['id' => $user->id, 'name' => $user->name]
        : false;
});

// DM chat — private channel (two users only)
Broadcast::channel('chat.dm.{dmId}', function ($user, $dmId) {
    $dm = \App\Models\DmChannel::find($dmId);
    return $dm && ($dm->user_one_id === $user->id || $dm->user_two_id === $user->id);
});
