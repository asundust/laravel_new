<?php

use Illuminate\Support\Facades\Route;

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
Route::get('t/{fun?}', 'Tests\TestController@t');
Route::get('wechatOauthTest', 'Tests\TestController@wechatOauthTest')->middleware(['wechat.oauth:default,snsapi_base']); // 微信授权测试

// 主页
Route::get('/', function () {
    return view('welcome');
});

// 支付通知
Route::any('notify/alipay', 'NotifyController@notifyAlipay'); // 异步通知 - 支付宝 - 支付
Route::any('return/alipay', 'NotifyController@returnAlipay'); // 同步返回 - 支付宝 - 跳转
Route::any('notify/wechat', 'NotifyController@notifyWechat'); // 异步通知 - 微信 - 支付
Route::any('notify/wechat_refund', 'NotifyController@notifyWechatRefund'); // 异步通知 - 微信 - 退款
