<?php

namespace App\Http\Controllers\Service\Pay;

use App\Models\Pay\MultiBill;
use Exception;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Log;

class WechatPayService
{
    /**
     * 发起支付请求
     *
     * @param array $data => ['no' => '订单号', 'amount' => '金额', 'name' => '订单名称']
     * @param string $drive
     * @param array $extData
     * @param string $custom
     * @return mixed
     * @throws Exception
     */
    public function pay($data, $drive = '', $extData = [], $custom = '')
    {
        $subject = strlen($data['name']) > 18 ? $data['name'] : config('app.name') . ' - ' . $data['name'];
        $order = [
            'out_trade_no' => $data['no'],
            'total_fee' => $data['amount'] * 100,
            'body' => $subject,
        ];
        if ($custom) {
            $order['attach'] = urlencode($custom);
        }
        if (count($extData) > 0) {
            foreach ($extData as $key => $value) {
                $order[$key] = $value;
            }
        }

        if (!$drive) {
            if (Agent::isMobile()) {
                $drive = 'wap';
            } else {
                throw new Exception('必须指定一种下单方式');
            }
        }

        $wechat = Pay::wechat()->$drive($order);
        if ($wechat->result_code == 'SUCCESS' && $wechat->return_code == 'SUCCESS') {
            if ($wechat->trade_type == 'NATIVE') {
                return $wechat->only(['trade_type', 'code_url']);
            }
            return $wechat; // todo 暂时先这样处理，因为不知道其他的情况
        }
        throw new Exception('未知情况');
    }

    /**
     * 支付回调异步通知
     *
     * @return string
     */
    public function notify()
    {
        $wechat = Pay::wechat();
        DB::beginTransaction();
        try {
            $data = $wechat->verify(); // 是的，验签就这么简单！
            if ($data->appid == config('pay.wechat.app_id') && $data->result_code == 'SUCCESS' && $data->return_code == 'SUCCESS') {
                $data->pay_no = $data->out_trade_no; // 统一变量名，支付商户订单号
                $data->pay_service_no = $data->transaction_id; // 统一变量名，支付商订单号
                $data->amount = $data->total_fee / 100; // 统一变量名，订单金额
                $result = MultiBill::handleNotify($data, 1);
                if ($result) {
                    DB::commit();
                    return $wechat->success();
                } else {
                    DB::rollBack();
                    return '';
                }
            }
            Log::debug('Alipay notify verify fail', $data->all());
            DB::rollBack();
            return '';
        } catch (Exception $e) {
            pl('微信回调失败：' . $e->getMessage(), 'notify', 'wechat');
            DB::rollBack();
            return '';
        }
    }
}