<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public const ALLOW_LISTS = [
        // '',
    ];

    /**
     * TestController constructor.
     */
    public function __construct()
    {
        if (!app()->isLocal() && in_array(__FUNCTION__, self::ALLOW_LISTS)) {
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
        dd('结束运行方法：' . $fun);
    }

    // public function a()
    // {
    //     //
    // }

    public function a()
    {
        //
    }
}
