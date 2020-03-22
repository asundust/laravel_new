<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Service\Pay\AlipayService;
use App\Http\Controllers\Service\Pay\WechatPayService;
use Exception;

class NotifyController
{
    /**
     * 微信异步通知入口 - 支付
     *
     * @return string
     * @throws Exception
     */
    public function notifyWechat()
    {
        return (new WechatPayService())->notify();
    }

    /**
     * 微信异步通知入口 - 退款
     *
     * @return string
     * @throws Exception
     */
    public function notifyWechatRefund()
    {
        return (new WechatPayService())->notifyRefund();
    }

    /**
     * 支付宝异步通知入口
     *
     * @return string
     * @throws Exception
     */
    public function notifyAlipay()
    {
        return (new AlipayService())->notify();
    }

    /**
     * 支付宝同步通知入口
     *
     * @return mixed
     */
    public function returnAlipay()
    {
        return (new AlipayService())->return();
    }
}