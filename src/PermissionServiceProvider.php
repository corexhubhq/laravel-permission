<?php

namespace Corexhubhq\LaravelPermission;

use Illuminate\Support\ServiceProvider;
use Corexhubhq\LaravelPermission\Services\PermissionService;

class PermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/permission.php', 'permission');

        $this->app->singleton(PermissionService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/permission.php' => config_path('permission.php'),
            ], 'permission-config');

            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'permission-migrations');
        }
    }
}
