<?php

namespace App\Models\Pay;

use App\Models\BaseModel;
use App\Models\BaseModelTrait;

class DemoOrder extends BaseModel
{
    use BaseModelTrait;

    const STATUS = [
        0 => '未支付',
        1 => '已支付',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (DemoOrder $order) {
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

    /**
     * 支付成功处理
     *
     * @param $bill
     */
    public function handlePay($bill)
    {
        $this->update([
            'pay_at' => $bill->pay_at,
            'status' => 1,
        ]);
    }

    /**
     * 支付宝支付成功处理同步页
     */
    public function payResult()
    {
        if ($this->status != 1) {
            return '支付失败';
        }
        return '支付成功';
    }
}
