<?php

namespace App\Providers;

use App\Models\Track;
use App\Models\Topic;
use App\Models\Quiz;
use App\Policies\TrackPolicy;
use App\Policies\TopicPolicy;
use App\Policies\QuizPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Track::class => TrackPolicy::class,
        Topic::class => TopicPolicy::class,
        Quiz::class => QuizPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}