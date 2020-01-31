<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * TestController constructor.
     */
    public function __construct()
    {
        if (app()->isProduction()) {
            abort(404);
        }
    }

    public function test($name = '')
    {
        if (strlen($name) == 0) {
            $name = 'a';
        }
        $result = $this->$name();
        if (!empty($result)) {
            return $result;
        }
        dd('结束运行方法：' . $name);
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
