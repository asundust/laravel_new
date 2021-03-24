<?php

namespace App\Http\Service\Pay;

use App\Models\Pay\MultiBill;
use Exception;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Facades\Agent;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Log;

class AlipayService
{
    /**
     * 发起支付请求
     *
     * @param array  $data
     *                        ['pay_no' => '商户单号', 'pay_amount' => '金额', 'title' => '订单名称']
     * @param string $drive
     * @param array  $extData
     * @param string $custom
     *
     * @return mixed
     */
    public function pay($data, $drive = '', $extData = [], $custom = '')
    {
        $order = [
            'out_trade_no' => $data['pay_no'],
            'total_amount' => $data['pay_amount'],
            'subject' => $data['title'],
        ];
        if (strlen($custom) > 0) {
            $order['passback_params'] = urlencode($custom);
        }
        foreach ($extData as $key => $value) {
            $order[$key] = $value;
        }

        if (!$drive) {
            if (Agent::isMobile()) {
                $drive = 'wap';
            } else {
                $drive = 'web';
            }
        }

        return Pay::alipay()->$drive($order);
    }

    /**
     * 支付回调异步通知.
     *
     * @return string
     *
     * @throws Exception
     */
    public function notify()
    {
        $alipay = Pay::alipay();
        DB::beginTransaction();
        try {
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 根据支付宝的文档 退款会有异步消息发送，但不作为参考，故丢弃此部分消息。参见下方链接
            // https://opensupport.alipay.com/support/knowCategory/27585/29211
            // https://opensupport.alipay.com/support/knowledge/27585/201602348776
            // https://opensupport.alipay.com/support/knowledge/27585/201602240500
            if (isset($data->gmt_refund)) {
                DB::commit();

                return '';
            }
            if (
                $data->app_id == config('pay.alipay.app_id')
                && in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])
            ) {
                $result = $this->paySuccessHandle($data);
                if ($result) {
                    DB::commit();

                    return $alipay->success();
                } else {
                    DB::rollBack();

                    return '';
                }
            }
            Log::debug('Alipay notify verify fail', $data->all());
            DB::rollBack();

            return '';
        } catch (Exception $e) {
            pl('支付宝回调失败：'.$e->getMessage(), 'alipay-notify', 'pay');
            DB::rollBack();

            return '';
        }
    }

    /**
     * 支付成功数据处理.
     *
     * @param $data
     *
     * @return bool
     */
    public function paySuccessHandle($data)
    {
        $data->pay_no = $data->out_trade_no; // 统一变量名，支付商户订单号
        $data->pay_service_no = $data->trade_no; // 统一变量名，支付商订单号
        $data->pay_amount = $data->total_amount; // 统一变量名，订单金额

        return MultiBill::handleNotify($data, 2);
    }

    /**
     * 支付回调同步通知.
     *
     * @return mixed
     *
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function return()
    {
        $data = Pay::alipay()->verify(); // 是的，验签就这么简单！

        return MultiBill::handleReturn($data->out_trade_no);
    }

    /**
     * 支付订单查询.
     *
     * @param $data
     *
     * @return \Yansongda\Supports\Collection
     *
     * @throws GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function payFind($data)
    {
        return $this->find($data);
    }

    /**
     * 支付订单查询结果处理.
     *
     * @param $data
     *
     * @return bool
     */
    public function payFindResultHandle($data)
    {
        if (
            '10000' == $data->code
            && in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])
        ) {
            return $this->paySuccessHandle($data);
        }

        return false;
    }

    /**
     * 发起退款请求
     *
     * @param array $data    => ['refund_amount' => '金额', 'pay_no' => '商户单号', 'pay_service_no' => '支付宝订单号', 'refund_no' => '退款单号'] | pay_no 和 pay_service_no 不能同时为空 | refund_no 部分退款时不能为空
     * @param array $extData
     *
     * @return mixed
     *
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function refund($data, $extData = [])
    {
        $refund = [
            'refund_amount' => $data['refund_amount'],
        ];
        if (isset($data['pay_no'])) {
            $refund['out_trade_no'] = $data['pay_no'];
        }
        if (isset($data['pay_service_no'])) {
            $refund['trade_no'] = $data['pay_service_no'];
        }
        if (isset($data['refund_no'])) {
            $refund['out_request_no'] = $data['refund_no'];
        }
        foreach ($extData as $key => $value) {
            $refund[$key] = $value;
        }
        try {
            $result = Pay::alipay()->refund($refund);

            return $this->refundResult($result);
        } catch (GatewayException $exception) {
            $return = [
                'code' => 1,
                'msg' => $exception->raw['alipay_trade_refund_response']['sub_msg'] ?? '',
                'service_code' => '',
                'service_msg' => $exception->raw['alipay_trade_refund_response']['sub_msg'] ?? '',
            ];
            Log::debug('Alipay refundResult() - 1', $return);

            return $return;
        }
    }

    /**
     * 退款结果处理.
     *
     * @param $result
     *
     * @return array
     */
    private function refundResult($result)
    {
        if (10000 == $result->code && 'Success' == $result->msg) {
            if ('Y' == $result->fund_change) {
                return [
                    'code' => 0,
                    'msg' => '退款处理成功',
                    'data' => [
                        'refund_no' => null,
                        'refund_service_no' => null,
                        'refund_amount' => $result->refund_fee,
                        'refund_at' => $result->gmt_refund_pay,
                    ],
                ];
            }
            $return = [
                'code' => 1,
                'msg' => '该订单已退款完成',
                'service_code' => $result->code,
                'service_msg' => $result->msg,
            ];
            Log::debug('Alipay refundResult() - 1', $return);

            return $return;
        }
        $return = [
            'code' => 1,
            'msg' => '退款失败',
            'service_code' => $result->code,
            'service_msg' => $result->msg,
        ];
        Log::debug('Alipay refundResult() - 2', $return);

        return $return;
    }

    /**
     * 退款订单查询.
     *
     * @param $data
     *
     * @return \Yansongda\Supports\Collection
     *
     * @throws GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function refundFind($data)
    {
        return $this->find($data, 'refund');
    }

    /**
     * 转账订单查询.
     *
     * @param $data
     *
     * @return \Yansongda\Supports\Collection
     *
     * @throws GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function transferFind($data)
    {
        return $this->find($data, 'transfer');
    }

    /**
     * 查询订单.
     *
     * @param string|array $data
     *                           支付 => 商户单号 | ['pay_no' => '商户单号', 'pay_service_no' => '支付宝订单号'](2选1)
     *                           退款 => 退款单号 | ['pay_no' => '商户单号', 'pay_service_no' => '支付宝订单号', 'refund_no' => '退款单号'](refund_no 为空时自动取值 pay_no)
     *                           转账 => 转账单号 | ['transfer_no' => '转账单号']
     * @param string       $type
     *                           '' => '支付', 'refund' => '退款', 'transfer' => '转账'
     *
     * @return \Yansongda\Supports\Collection
     *
     * @throws GatewayException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function find($data, $type = '')
    {
        $alipay = Pay::alipay();
        $order = [];
        switch ($type) {
            case 'refund':
                if (isset($data['pay_no'])) {
                    $order['out_trade_no'] = $data['pay_no'];
                }
                if (isset($data['pay_service_no'])) {
                    $order['trade_no'] = $data['pay_service_no'];
                }
                $order['out_request_no'] = $data['refund_no'] ?? $data['pay_no'] ?? '';

                return $alipay->find($order, $type);
                break;
            case 'transfer':
                if (!is_array($data)) {
                    $order = ['out_trade_no' => $data];
                } else {
                    if (isset($data['transfer_no'])) {
                        $order['out_trade_no'] = $data['transfer_no'];
                    }
                }

                return $alipay->find($order, $type);
                break;
            default:
                if (!is_array($data)) {
                    $order = ['out_trade_no' => $data];
                } else {
                    if (isset($data['pay_no'])) {
                        $order['out_trade_no'] = $data['pay_no'];
                    }
                    if (isset($data['pay_service_no'])) {
                        $order['trade_no'] = $data['pay_service_no'];
                    }
                }

                return $alipay->find($order);
                break;
        }
    }
}
