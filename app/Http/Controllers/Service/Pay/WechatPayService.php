<?php

namespace App\Http\Controllers\Service\Pay;

use App\Models\Pay\MultiBill;
use App\Models\Pay\MultiRefundBill;
use Exception;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Log;

class WechatPayService
{
    /**
     * 发起支付请求
     *
     * @param array $data => ['pay_no' => '订单号', 'pay_amount' => '金额', 'title' => '订单名称']
     * @param string $drive
     * @param array $extData
     * @param string $custom
     * @return mixed
     * @throws Exception
     */
    public function pay($data, $drive = '', $extData = [], $custom = '')
    {
        $order = [
            'out_trade_no' => $data['pay_no'],
            'total_fee' => $data['pay_amount'] * 100,
            'body' => $data['title'],
        ];
        if (strlen($custom) > 0) {
            $order['attach'] = urlencode($custom);
        }
        foreach ($extData as $key => $value) {
            $order[$key] = $value;
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
     * 发起退款请求
     *
     * @param array $data => ['pay_amount' => '订单金额', 'refund_amount' => '退款金额', 'pay_no' => '订单号', 'pay_service_no' => '微信订单号', 'refund_no' => '退款单号'] | no 和 service_no 不能同时为空
     * @param array $extData
     * @return mixed
     */
    public function refund($data, $extData = [])
    {
        $refund = [
            'total_fee' => $data['pay_amount'] * 100,
            'refund_fee' => $data['refund_amount'] * 100,
            'out_refund_no' => $data['refund_no'],
            'notify_url' => config('pay.wechat.notify_refund_url'),
        ];
        if (isset($data['pay_no'])) {
            $refund['out_trade_no'] = $data['pay_no'];
        }
        if (isset($data['pay_service_no'])) {
            $refund['transaction_id'] = $data['pay_service_no'];
        }
        foreach ($extData as $key => $value) {
            $refund[$key] = $value;
        }
        try {
            $result = Pay::wechat()->refund($refund);
            return $this->refundResult($result);
        } catch (GatewayException $exception) {
            $return = [
                'code' => 1,
                'msg' => $exception->raw['err_code_des'],
                'service_code' => $exception->raw['err_code'] ?? $exception->raw['return_code'],
                'service_msg' => $exception->raw['err_code_des'] ?? $exception->raw['return_msg'],
            ];
            Log::debug('Wechat refund()', $return);
            return $return;
        }
    }

    /**
     * 退款结果处理
     *
     * @param $result
     * @return array
     */
    private function refundResult($result)
    {
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS' && $result->appid == config('pay.wechat.app_id') && $result->mch_id == config('pay.wechat.mch_id')) {
            return [
                'code' => 0,
                'msg' => '退款申请成功',
                'data' => [
                    'refund_no' => $result->out_refund_no,
                    'refund_service_no' => $result->refund_id,
                    'refund_amount' => $result->refund_fee / 100,
                    'refund_at' => null,
                    'pay_no' => $result->out_trade_no,
                    'pay_service_no' => $result->transaction_id,
                    'pay_amount' => $result->total_fee / 100,
                ],
            ];
        }
        $return = [
            'code' => 1,
            'msg' => $result->err_code_des,
            'service_code' => $result->err_code ?? $result->return_code,
            'service_msg' => $result->err_code_des ?? $result->return_msg,
        ];
        Log::debug('Wechat refundResult()', $return);
        return $return;
    }

    /**
     * 支付回调异步通知
     *
     * @return string
     * @throws Exception
     */
    public function notify()
    {
        $wechat = Pay::wechat();
        DB::beginTransaction();
        try {
            $data = $wechat->verify(); // 是的，验签就这么简单！
            if ($data->return_code == 'SUCCESS' && $data->result_code == 'SUCCESS' && $data->appid == config('pay.wechat.app_id') && $data->mch_id == config('pay.wechat.mch_id')) {
                $data->pay_no = $data->out_trade_no; // 统一变量名，支付商户订单号
                $data->pay_service_no = $data->transaction_id; // 统一变量名，支付商订单号
                $data->pay_amount = $data->total_fee / 100; // 统一变量名，订单金额
                $result = MultiBill::handleNotify($data, 1);
                if ($result) {
                    DB::commit();
                    return $wechat->success();
                } else {
                    DB::rollBack();
                    return '';
                }
            }
            Log::debug('Wechat notify verify fail', $data->all());
            DB::rollBack();
            return '';
        } catch (Exception $e) {
            pl('微信回调失败：' . $e->getMessage(), 'wechat-notify', 'pay');
            DB::rollBack();
            return '';
        }
    }

    /**
     * 退款回调异步通知
     *
     * @return string
     * @throws Exception
     */
    public function notifyRefund()
    {
        $wechat = Pay::wechat();
        DB::beginTransaction();
        try {
            $data = $wechat->verify(null, true); // 是的，验签就这么简单！
            if ($data->return_code == 'SUCCESS' && $data->refund_status == 'SUCCESS' && $data->appid == config('pay.wechat.app_id') && $data->mch_id == config('pay.wechat.mch_id')) {
                $data->refund_no = $data->out_refund_no; // 统一变量名，支付商户退款订单号
                $data->refund_service_no = $data->refund_id; // 统一变量名，支付商退款订单号
                $data->refund_amount = $data->refund_fee / 100; // 统一变量名，退款金额
                $data->refund_at = $data->success_time; // 统一变量名，退款到账时间
                $data->pay_no = $data->out_trade_no; // 统一变量名，支付商户订单号
                $data->pay_service_no = $data->transaction_id; // 统一变量名，支付商订单号
                $data->pay_amount = $data->total_fee / 100; // 统一变量名，订单金额
                $result = MultiRefundBill::handleNotifyRrFund($data);
                if ($result) {
                    DB::commit();
                    return $wechat->success();
                } else {
                    DB::rollBack();
                    return '';
                }
            }
            Log::debug('Wechat refund notify verify fail', $data->all());
            DB::rollBack();
            return '';
        } catch (Exception $e) {
            pl('微信回调失败：' . $e->getMessage(), 'wechat-notify', 'pay');
            DB::rollBack();
            return '';
        }
    }
}