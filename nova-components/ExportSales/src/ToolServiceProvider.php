<?php

namespace Vendor\ExportSales;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Vendor\ExportSales\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('export-sales', __DIR__.'/../dist/js/tool.js');
            Nova::style('export-sales', __DIR__.'/../dist/css/tool.css');
        });
    }

    /**
     * Register the tool's routes.
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', 'nova.auth', Authorize::class], 'export-sales')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', 'nova.auth', Authorize::class])
            ->prefix('nova-vendor/export-sales')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
