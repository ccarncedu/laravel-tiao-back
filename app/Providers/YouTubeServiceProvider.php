<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\YouTubeService;

class YouTubeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(YouTubeService::class, function ($app) {
            return new YouTubeService();
        });
    }

    public function boot()
    {
        //
    }
}

