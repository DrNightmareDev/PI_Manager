<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager;

use DrNightmare\SeatPiManager\Console\Commands\ImportStaticPlanetsCommand;
use Illuminate\Routing\Router;
use Seat\Services\AbstractSeatPlugin;

class PiManagerServiceProvider extends AbstractSeatPlugin
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seat-pi-manager');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'seat-pi-manager');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerRoutes();
        $this->registerMiddleware($this->app->make(Router::class));

        $this->publishes([
            __DIR__ . '/../config/pi_manager.php' => config_path('seat-pi-manager.php'),
        ], 'seat-pi-manager-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pi_manager.php', 'seat-pi-manager');
        $this->mergeConfigFrom(__DIR__ . '/Config/package.sidebar.php', 'package.sidebar');
        $this->registerPermissions(__DIR__ . '/Config/Permissions/package.permissions.php', 'seat-pi-manager');
        $this->commands([
            ImportStaticPlanetsCommand::class,
        ]);
    }

    public function getName(): string
    {
        return 'SeAT PI Manager';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/DrNightmareDev/PI_Manager';
    }

    public function getPackagistVendorName(): string
    {
        return 'drnightmare';
    }

    public function getPackagistPackageName(): string
    {
        return 'seat-pi-manager';
    }

    protected function registerRoutes(): void
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__ . '/Http/routes.php';
        }
    }

    protected function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware('seat-pi-manager.scope', \DrNightmare\SeatPiManager\Http\Middleware\EnsureViewerCanAccessPiManager::class);
    }
}
