<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 测试
Route::get('test/{name?}', 'Tests\TestController@test');

// 主页
Route::get('/', function () {
    return view('welcome');
});
