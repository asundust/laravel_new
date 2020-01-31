<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Service\Pay\AlipayService;
use App\Http\Controllers\Service\Pay\WechatPayService;

class NotifyController
{
    /**
     * 微信异步通知入口
     *
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function notifyWechat()
    {
        return (new WechatPayService())->notify();
    }

    /**
     * 支付宝异步通知入口
     *
     * @return string|\Symfony\Component\HttpFoundation\Response
     * @return string
     */
    public function notifyAlipay()
    {
        return (new AlipayService())->notify();
    }

    /**
     * 支付宝同步通知入口
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function returnAlipay()
    {
        return (new AlipayService())->return();
    }
}