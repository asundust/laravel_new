<?php

namespace App\Models\Pay;

use App\Models\BaseModel;

class MultiBill extends BaseModel
{
    // 支付方式
    const PAY_WAY = [
        1 => '微信',
        2 => '支付宝',
    ];

    const PAY_WAY_ALIAS = [
        1 => 'wechat',
        2 => 'alipay',
    ];

    const STATUS = [
        1 => '未支付',
        2 => '付款成功',
        3 => '付款失败',
        4 => '付款取消',
        5 => '退款中',
        6 => '退款成功',
        7 => '退款失败',
    ];

    // 支付方式名称 pay_way
    public function getPayWayAttribute()
    {
        return self::PAY_WAY[$this->pay_way] ?? '';
    }

    /**
     * 支付回调通知处理
     *
     * @param $data
     * @param int $payWay
     * @return bool
     */
    public static function handleNotify($data, int $payWay)
    {
        $multiBill = self::where('pay_no', $data->pay_no)->where('pay_way', $payWay)->first();
        if (!$multiBill) {
            pl('找不到支付订单信息：' . $data->pay_no, self::PAY_WAY_ALIAS[$payWay] . '-notify-err', 'pay');
            return true;
        }

        if ($multiBill->status != 1) {
            // 本地订单状态已取消，安排退款
            if ($multiBill->status == 4) {
                // todo
                return true;
            }
            pl('订单状态非未支付：' . $data->pay_no . '，订单状态：' . $multiBill->status_name, self::PAY_WAY_ALIAS[$payWay] . '-notify-comment', 'pay');
            return true;
        }
        if ($multiBill->amount != $data->amount) {
            pl('订单支付金额不一致：' . $data->pay_no . '，订单金额：' . $multiBill->amount . '，回调金额：' . $data->amount, self::PAY_WAY_ALIAS[$payWay] . '-notify-comment', 'pay');
            return false;
        }

        $multiBill->fill([
            'pay_service_no' => $data->pay_service_no,
            'pay_at' => now(),
            'status' => 2,
        ]);

        $multiBill->save();

        // 业务逻辑发起 todo

        return true;
    }
}
