<?php

declare(strict_types=1);

namespace Dclaysmith\LaravelCascade\Providers;

use Dclaysmith\LaravelCascade\Commands\StartCommand;
// use Dclaysmith\LaravelCascade\Console\Commands\InstallCommand;
use Dclaysmith\LaravelCascade\Components\BlankLayout;
use Dclaysmith\LaravelCascade\Middleware\InjectJavascriptTrackingCode;
use Dclaysmith\LaravelCascade\Middleware\RequestLogger;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class LaravelCascadeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
    }

    public function boot(Kernel $kernel): void
    {
        $kernel = $this->app->make(Kernel::class);

        if (config('cascade.tracking.server.loadMiddleware') === true) {
            $kernel->pushMiddleware(RequestLogger::class);
        }

        if (config('cascade.tracking.client.loadMiddleware') === true) {
            $kernel->pushMiddleware(InjectJavascriptTrackingCode::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands(
                commands: [
                    // ExportSegments::class,
                    // LogSegmentStatistics::class,
                    // LogEntityStatistics::class,
                    // RollupEvents::class,
                    // RollupPageViews::class,
                    // RollupSessions::class,
                    // InstallCommand::class,
                    StartCommand::class,
                ],
            );
        }

        /**
         * Load Migrations to update the database
         */
        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ]);

        /**
         * Load resources used by this package
         */
        Blade::component('blank-layout', BlankLayout::class);

        /**
         * Publish resources used by this package
         */
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cascade');

        /**
         * Publish Config file
         */
        $this->publishes(
            [
                __DIR__.'/../../config/cascade.php' => config_path(
                    'cascade.php'
                ),
            ],
        );

        /**
         * Load Routes
         */
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}