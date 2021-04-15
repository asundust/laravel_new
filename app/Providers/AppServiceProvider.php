<?php

namespace App\Providers;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
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
            if (
                class_exists('\Encore\Admin\Config\Config')
                && class_exists('\App\Models\Admin\AdminConfig')
                && Schema::hasTable(config('admin.extensions.config.table', 'admin_config'))
            ) {
                if (Cache::missing(\App\Models\Admin\AdminConfig::CACHE_KEY_PREFIX)) {
                    \App\Models\Admin\AdminConfig::configLoad();
                }
            }
        } catch (Exception $exception) {
        }
    }
}
