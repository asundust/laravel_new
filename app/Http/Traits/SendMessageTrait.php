<?php

namespace App\Http\Traits;

use Asundust\WechatWorkPush\Http\Traits\WechatWorkPushSendMessageTrait;

trait SendMessageTrait
{
    use WechatWorkPushSendMessageTrait;

    /**
     * 发送消息.
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function sendMessage($text, $desc = '')
    {
        switch (cache_config('message_send_way')) {
            case 1:
                return sc_send($text, $desc);
                break;
            case 2:
                return sct_send($text, $desc);
                break;
            case 3:
                return $this->defaultSend(cache_config('wechat_work_push_user', '@all'), $text, $desc);
                break;
        }
    }
}
