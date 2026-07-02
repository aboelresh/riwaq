<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    /**
     * Send a notification to a single user.
     */
    public static function send(int $userId, string $type, string $title, ?string $body = null, ?array $data = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    /**
     * Send a notification to multiple users.
     */
    public static function sendToMany(array $userIds, string $type, string $title, ?string $body = null, ?array $data = null): void
    {
        $now = now();
        $records = array_map(fn($uid) => [
            'user_id' => $uid,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data ? json_encode($data) : null,
            'is_read' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ], $userIds);

        Notification::insert($records);
    }

    /**
     * Notify all team members except the actor.
     */
    public static function notifyTeamExcept(int $teamId, int $exceptUserId, string $type, string $title, ?string $body = null, ?array $extraData = null): void
    {
        $memberIds = \App\Models\TeamMember::where('team_id', $teamId)
            ->where('user_id', '!=', $exceptUserId)
            ->pluck('user_id')
            ->toArray();

        if (empty($memberIds)) return;

        $data = array_merge(['team_id' => $teamId], $extraData ?? []);
        self::sendToMany($memberIds, $type, $title, $body, $data);
    }
}
