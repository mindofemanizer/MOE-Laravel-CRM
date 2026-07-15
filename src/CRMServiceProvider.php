<?php
declare(strict_types=1);

namespace Moe\CRM;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CRMServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(\Moe\CRM\Services\ActivityService::class);
        $this->app->singleton(\Moe\CRM\Services\SegmentService::class);
        $this->app->singleton(\Moe\CRM\Services\LeadService::class);
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/crm.php' => config_path('crm.php'),
        ], 'crm-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'crm-migrations');

        $this->registerEventListeners();
    }

    /**
     * @return void
     */
    protected function registerEventListeners(): void
    {
        $listener = 'Moe\\CRM\\Listeners\\LogCustomerActivityOnOrderEvent';

        if (class_exists('Moe\\Commerce\\Events\\OrderPlaced')) {
            Event::listen(
                'Moe\\Commerce\\Events\\OrderPlaced',
                [$listener, 'handleOrderPlaced']
            );
        }

        if (class_exists('Moe\\Commerce\\Events\\OrderStatusChanged')) {
            Event::listen(
                'Moe\\Commerce\\Events\\OrderStatusChanged',
                [$listener, 'handleOrderCompleted']
            );
        }
    }
}
