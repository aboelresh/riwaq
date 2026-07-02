<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ProgressService;
use App\Services\QuizService;
use App\Services\UnlockService;
use App\Services\VideoService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProgressService::class, function ($app) {
            return new ProgressService();
        });

        $this->app->singleton(QuizService::class, function ($app) {
            return new QuizService();
        });

        $this->app->singleton(UnlockService::class, function ($app) {
            return new UnlockService(
                $app->make(ProgressService::class),
                $app->make(QuizService::class)
            );
        });

        $this->app->singleton(VideoService::class, function ($app) {
            return new VideoService();
        });
    }

    public function boot(): void
    {
        //
    }
}