<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EnvironmentalCorrelationService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

public function register()
{
    $this->app->singleton(EnvironmentalCorrelationService::class, function ($app) {
        return new EnvironmentalCorrelationService();
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    // app/Providers/AppServiceProvider.php


}
