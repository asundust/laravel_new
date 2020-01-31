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

// 支付通知
Route::any('notify/alipay', 'NotifyController@notifyAlipay'); // 异步通知 - 支付宝
Route::any('notify/wechat', 'NotifyController@notifyWechat'); // 异步通知 - 微信
Route::any('return/alipay', 'NotifyController@returnAlipay'); // 同步返回 - 支付宝
