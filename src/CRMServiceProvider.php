<?php

namespace Moe\CRM;

use Illuminate\Support\ServiceProvider;

class CRMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Moe\CRM\Services\ActivityService::class);
        $this->app->singleton(\Moe\CRM\Services\SegmentService::class);
        $this->app->singleton(\Moe\CRM\Services\LeadService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/crm.php' => config_path('crm.php'),
        ], 'crm-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'crm-migrations');
    }
}
