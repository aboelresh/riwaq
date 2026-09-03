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
       RateLimiter::for('auth', function (Request $request) {
    $limit = app()->environment('production') ? 5 : 60;
    return Limit::perMinute($limit)->by($request->ip());
    });


        RateLimiter::for('sensitive', function (Request $request) {
        $limit = app()->environment('production') ? 10 : 120;
         return Limit::perMinute($limit)
        ->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api', function (Request $request) {
        $limit = app()->environment('production') ? 60 : 300;
        return Limit::perMinute($limit)
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