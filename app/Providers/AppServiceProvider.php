<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 引入Admin Config配置
        try {
            if (class_exists('\Encore\Admin\Config\Config') && \Illuminate\Support\Facades\Schema::hasTable(config('admin.extensions.config.table', 'admin_config'))) {
                \Encore\Admin\Config\Config::load();
            }
        } catch (Exception $exception) {
        }
    }
}
