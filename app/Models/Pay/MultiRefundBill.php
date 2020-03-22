<?php

namespace App\Models\Pay;

use App\Events\BillRefundedEvent;
use App\Models\BaseModel;

/**
 * App\Models\Pay\MultiRefundBill
 *
 * @property int $id
 * @property int $multi_bill_id 支付订单号id
 * @property float $refund_amount 退款发起金额
 * @property string|null $refund_no 退款商户订单号
 * @property string|null $refund_service_no 退款支付商订单号
 * @property string|null $refund_at 退款到账时间
 * @property int $refund_status 退款状态(1退款中，2退款成功，3退款失败，4退款取消)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $refund_status_name
 * @property-read mixed $status_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereMultiBillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereRefundAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereRefundNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereRefundServiceNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\MultiRefundBill whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MultiRefundBill extends BaseModel
{
    const REFUND_STATUS = [
        1 => '未支付',
        2 => '付款成功',
        3 => '付款失败',
        4 => '付款取消',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $refundBill) {
            if (!$refundBill->refund_status) {
                $refundBill->refund_status = 1;
            }
            if (!$refundBill->refund_no) {
                $refundBill->refund_no = self::getNewNumber('', 'refund_no');
            }
        });
    }

    // 退款状态名称 refund_status_name
    public function getRefundStatusNameAttribute()
    {
        return self::REFUND_STATUS[$this->refund_status] ?? '';
    }

    /**
     * 退款回调通知处理 - 异步
     * 目前只有微信
     *
     * @param $data
     * @return bool
     */
    public static function handleNotifyRrFund($data)
    {
        $refundBill = self::where('refund_no', $data->refund_no)->first();
        if (!$refundBill) {
            pl('找不到退款订单信息：' . $data->refund_no, 'wechat' . '-notify', 'pay');
            return true;
        }
        if ($refundBill->refund_status != 1) {
            pl('退款订单状态非退款中：' . $data->refund_no . '，订单状态：' . $refundBill->refund_status_name, 'wechat' . '-notify-comment', 'pay');
            return true;
        }
        if ($refundBill->refund_amount != $data->refund_amount) {
            pl('退款订单金额不一致：' . $data->refund_no . '，订单金额：' . $refundBill->refund_no . '，回调金额：' . $data->refund_no, 'wechat' . '-notify', 'pay');
            return false;
        }

        $refundBill->fill([
            'refund_service_no' => $data->refund_service_no,
            'refund_at' => now(),
            'refund_status' => 2,
        ]);
        $refundBill->save();
        event(new BillRefundedEvent($refundBill));
        return true;
    }
}
