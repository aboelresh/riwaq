<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Auth routes: login, register, forgot-password
        // 5 requests per minute per IP — stops brute force
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Sensitive routes: AI chat, video upload
        // 10 requests per minute per user
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip());
        });

        // General API: all authenticated routes
        // 60 requests per minute per user
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip());
        });

            // Domain Events
\Illuminate\Support\Facades\Event::listen(
    \App\Events\TopicCompleted::class,
    [\App\Listeners\SendMilestoneNotification::class, 'handleTopicCompleted']
);

\Illuminate\Support\Facades\Event::listen(
    \App\Events\CourseCompleted::class,
    [\App\Listeners\SendMilestoneNotification::class, 'handleCourseCompleted']
);

\Illuminate\Support\Facades\Event::listen(
    \App\Events\TrackCompleted::class,
    [\App\Listeners\SendMilestoneNotification::class, 'handleTrackCompleted']
);
    }
}