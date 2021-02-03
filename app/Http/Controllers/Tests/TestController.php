<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;
use App\Http\Traits\WechatTrait;

class TestController extends Controller
{
    use WechatTrait;

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        if (app()->isProduction() && in_array(__FUNCTION__, ['wechatOauthTest'])) {
            abort(404);
        }
    }

    public function t($fun = '')
    {
        if (0 == strlen($fun)) {
            $fun = 'a';
        }
        $result = $this->$fun();
        if (!empty($result)) {
            return $result;
        }
        dd('结束运行方法：'.$fun);
    }

    public function wechatOauthTest()
    {
        dd($this->getWechatUser());
    }

    // public function a()
    // {
    //     //
    // }

    public function a()
    {
    }
}
