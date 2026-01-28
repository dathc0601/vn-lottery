<?php

namespace App\Providers;

use App\Services\FooterService;
use App\Services\NavigationService;
use App\Services\SeoOverrideService;
use App\Services\SiteSettingsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NavigationService::class, function ($app) {
            return new NavigationService();
        });

        $this->app->singleton(SiteSettingsService::class, function ($app) {
            return new SiteSettingsService();
        });

        $this->app->singleton(FooterService::class, function ($app) {
            return new FooterService($app->make(SiteSettingsService::class));
        });

        $this->app->singleton(SeoOverrideService::class, function ($app) {
            return new SeoOverrideService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
