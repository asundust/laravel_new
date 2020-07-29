<?php

namespace App\Providers;

use Carbon\Carbon;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 数据库字符串字段长度
        Schema::defaultStringLength(191);
        // Carbon中文
        Carbon::setLocale('zh');

        // 引入网站配置
        if (class_exists('\Encore\Admin\Config\Config') && \Illuminate\Support\Facades\Schema::hasTable(config('admin.extensions.config.table', 'admin_config'))) {
            \Encore\Admin\Config\Config::load();
        }
    }
}
