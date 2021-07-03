<?php

namespace Asundust\PushLaravel;

use Illuminate\Support\ServiceProvider;

class PushLaravelServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/push-laravel.php', 'push-laravel');

        // Register the service the package provides.
        foreach (config('push-laravel') as $account => $config) {
            $this->app->singleton("push-laravel.{$account}", function () use ($config) {
                return new PushLaravel($config);
            });
        }

        $this->app->alias('push-laravel.default', 'push-laravel');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['push-laravel'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/push-laravel.php' => config_path('push-laravel.php'),
        ], 'push-laravel.config');
    }
}
