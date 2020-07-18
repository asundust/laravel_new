<?php

namespace App\Models\Pay;

use App\Models\BaseModel;
use App\Models\BaseModelTrait;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Pay\DemoOrder
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $number 订单号
 * @property string $title 订单名称
 * @property float $price 价格
 * @property string|null $pay_at 支付时间
 * @property int $status 状态(0未付款，1已付款)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pay\MultiBill $bill
 * @property-read \App\Models\Pay\MultiBill $billed
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pay\MultiBill[] $bills
 * @property-read int|null $bills_count
 * @property-read mixed $can_refund_amount
 * @property-read mixed $payed_amount
 * @property-read mixed $refunded_amount
 * @property-read mixed $refunding_amount
 * @property-read mixed $status_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder wherePayAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pay\DemoOrder whereUserId($value)
 * @mixin \Eloquent
 */
class DemoOrder extends BaseModel
{
    use BaseModelTrait;

    const STATUS = [
        0 => '未支付',
        1 => '已支付',
        2 => '部分退款',
        3 => '全额退款',
    ];

    const STATUS_LABEL = [
        0 => 'default',
        1 => 'success',
        2 => 'warning',
        3 => 'danger',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $order) {
            if (!$order->number) {
                $order->number = self::getNewNumber(self::class);
            }
            if (!$order->status) {
                $order->status = 0;
            }
        });
    }

    public function bills()
    {
        return $this->morphMany(MultiBill::class, 'billable');
    }

    public function bill()
    {
        return $this->morphOne(MultiBill::class, 'billable')->latest();
    }

    public function billed()
    {
        return $this->morphOne(MultiBill::class, 'billable')->where('bill_status', 1)->latest();
    }

    // 已支付的金额 payed_amount
    public function getPayedAmountAttribute()
    {
        return $this->billed ? $this->billed->pay_amount : 0.00;
    }

    // 已退款的金额 refunded_amount
    public function getRefundedAmountAttribute()
    {
        return $this->billed ? $this->billed->refunded_amount : 0.00;
    }

    // 退款中的金额 refunding_amount
    public function getRefundingAmountAttribute()
    {
        return $this->billed ? $this->billed->refunding_amount : 0.00;
    }

    // 可退款的金额 can_refund_amount
    public function getCanRefundAmountAttribute()
    {
        return $this->billed ? $this->billed->can_refund_amount : 0.00;
    }

    /**
     * 获取支付结果页面链接
     *
     * @return string
     */
    public function payResultUrl()
    {
        return route('web.pay_result', ['id' => $this->id]);
    }

    /**
     * 支付成功处理
     *
     * @param MultiBill $bill
     */
    public function handlePied($bill)
    {
        Cache::put('DemoOrder' . $this->id, 1, 600);
        $this->update([
            'pay_at' => $bill->pay_at,
            'status' => 1,
        ]);
        // 发送Server酱推送通知
        sc_send(config('app.name') . $bill->pay_way_name . '有一笔新的收款' . money_show($this->payed_amount) . '元', $bill->pay_way_name . '于 ' . $bill->pay_at . ' 收款：￥' . money_show($this->payed_amount));
    }

    /**
     * 支付宝支付成功处理同步页
     */
    public function payResult()
    {
        return redirect($this->payResultUrl());
    }

    /**
     * 退款处理
     *
     * @param MultiBill $bill
     */
    public function handleRefunded($bill)
    {
        if ($bill->pay_status == 5) {
            $this->status = 2;
            $this->save();
        } elseif ($bill->pay_status == 6) {
            $this->status = 3;
            $this->save();
        }
    }
}
