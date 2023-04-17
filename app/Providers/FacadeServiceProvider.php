<?php

namespace App\Providers;

use App\Service\ConsoleService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('consoleservice', fn($app) => new ConsoleService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {}
}
