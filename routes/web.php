<?php

use App\Http\Controllers\Tests\TestController;
use Illuminate\Support\Facades\Route;

// 测试
Route::get('t/{fun?}', [TestController::class, 't']);

// 主页
Route::get('/', function () {
    return view('welcome');
});
