<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AuditLogService
{

    public static function log(
        string $action,
        string $resource,
        int|string|null $resourceId = null,
        array $context = []
    ): void {
        $user = auth()->user();

        Log::channel('audit')->info($action, array_merge([
            'resource'    => $resource,
            'resource_id' => $resourceId,
            'actor_id'    => $user?->id,
            'actor_email' => $user?->email,
            'actor_role'  => $user?->role,
            'ip'          => request()->ip(),
            'url'         => request()->fullUrl(),
            'timestamp'   => now()->toIso8601String(),
        ], $context));
    }

    public static function created(string $resource, $model): void
    {
        self::log('CREATED', $resource, $model->id, ['data' => $model->toArray()]);
    }

    public static function updated(string $resource, $model, array $changes = []): void
    {
        self::log('UPDATED', $resource, $model->id, ['changes' => $changes]);
    }

    public static function deleted(string $resource, int|string $id): void
    {
        self::log('DELETED', $resource, $id);
    }
}