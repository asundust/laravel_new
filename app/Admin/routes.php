<?php

use App\Admin\Controllers\ConfigController;
use App\Admin\Controllers\HomeController;
use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix') . '.',
], function (Router $router) {
    $router->get('/', [HomeController::class, 'index'])->name('admin.home');
    $router->get('config/refresh', [ConfigController::class, 'refresh'])->name('admin.config.refresh'); // 刷新配置缓存
});
