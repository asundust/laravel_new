<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;

// use App\Http\Traits\WechatTrait;

class TestController extends Controller
{
    // use WechatTrait;

    public const ALLOW_LISTS = [
        // 'wechatOauthTest',
    ];

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        if (! app()->isLocal() && in_array(__FUNCTION__, self::ALLOW_LISTS)) {
            abort(404);
        }
    }

    public function t($fun = '')
    {
        if (0 == strlen($fun)) {
            $fun = 'a';
        }
        $result = $this->$fun();
        if (! empty($result)) {
            return $result;
        }
        dd('结束运行方法：' . $fun);
    }

    // public function wechatOauthTest()
    // {
    //     $wechatUser = $this->getWechatUser();
    //     mad('授权成功，您的用户id为：'.$wechatUser->user_id.' ，您的openid为：'.$wechatUser->wechat_openid);
    // }

    // public function a()
    // {
    //     //
    // }

    public function a()
    {
    }
}
