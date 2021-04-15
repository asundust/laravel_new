<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
    'as' => config('admin.route.prefix').'.',
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('config/refresh', 'ConfigController@refresh')->name('admin.config.refresh'); // 刷新配置缓存
});
