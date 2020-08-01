<?php

namespace App\Listeners;

use App\Events\BillRefundedEvent;
use App\Models\Pay\MultiBill;

class BillRefundedHandleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(BillRefundedEvent $event)
    {
        $bill = MultiBill::with(['refundBills'])->where('id', $event->refundBill->multi_bill_id)->first();
        if ($bill) {
            if ($bill->refunded_amount < $bill->pay_amount) {
                $bill->pay_status = 5;
                $bill->save();
                if (method_exists($bill->billable, 'handleRefunded')) {
                    $bill->billable->handleRefunded($bill);
                }
            } elseif ($bill->refunded_amount == $bill->pay_amount) {
                $bill->pay_status = 6;
                $bill->save();
                if (method_exists($bill->billable, 'handleRefunded')) {
                    $bill->billable->handleRefunded($bill);
                }
            } else {
                pl('退款id:'.$event->refundBill->id.'|支付id'.$bill->id.'|支付订单号'.$bill->pay_no.'|'.'!!!退款总金额超过支付金额!!!', 'refund_bill', 'bill');
            }
        } else {
            pl('退款id:'.$event->refundBill->id.'|支付id:'.$event->refundBill->multi_bill_id.'|'.'!!!支付订单信息不存在!!!', 'refund_bill', 'bill');
        }
    }
}
