<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

        // // 强制https // use Illuminate\Support\Facades\URL
        // if (config('force_https') == 1) {
        //     URL::forceScheme('https');
        // }

        // sql日志记录
        if (app()->isLocal()) {
            DB::listen(function ($q) {
                if (strpos($q->sql, config(config('database.default') . 'connections.mysql.prefix') . 'telescope') === false) {
                    $bindings = array_map(function ($v) {
                        return is_time_string($v) ? sprintf("'%s'", $v) : $v;
                    }, $q->bindings);
                    $realSql = Str::replaceArray('?', $bindings, $q->sql);
                    log_channel('sql')->info("{$realSql} {$q->time} ms");
                }
            });
        }
    }
}
