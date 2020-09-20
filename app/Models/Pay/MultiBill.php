<?php

namespace App\Models\Pay;

use App\Events\BillPayedEvent;
use App\Events\BillRefundedEvent;
use App\Http\Service\Pay\AlipayService;
use App\Http\Service\Pay\WechatPayService;
use App\Models\BaseModel;
use App\Models\BaseModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Pay\MultiBill.
 *
 * @property int                          $id
 * @property string                       $billable_type      多态模型名称
 * @property string|null                  $billable_id        多态模型id
 * @property int|null                     $user_id            用户id
 * @property string|null                  $openid             Openid(微信支付涉及)
 * @property string|null                  $title              订单名称
 * @property int|null                     $pay_way            支付方式(1微信，2支付宝)
 * @property float                        $pay_amount         支付发起金额
 * @property string                       $pay_no             商户订单号
 * @property string|null                  $pay_service_no     支付商订单号
 * @property string|null                  $pay_at             支付成功时间
 * @property int                          $bill_status        账单状态(0未支付成功过，1支付成功过)
 * @property int                          $pay_status         支付状态(1未支付，2付款成功，3付款失败，4付款取消)
 * @property Carbon|null                  $created_at
 * @property Carbon|null                  $updated_at
 * @property Model|Eloquent               $billable
 * @property mixed                        $bill_status_name
 * @property mixed                        $can_refund_amount
 * @property mixed                        $pay_status_name
 * @property mixed                        $pay_way_name
 * @property mixed                        $pay_way_alias
 * @property mixed                        $refunded_amount
 * @property mixed                        $refunding_amount
 * @property mixed                        $status_name
 * @property Collection|MultiRefundBill[] $refundBills
 * @property int|null                     $refund_bills_count
 *
 * @method static Builder|MultiBill newModelQuery()
 * @method static Builder|MultiBill newQuery()
 * @method static Builder|MultiBill query()
 * @method static Builder|MultiBill whereBillStatus($value)
 * @method static Builder|MultiBill whereBillableId($value)
 * @method static Builder|MultiBill whereBillableType($value)
 * @method static Builder|MultiBill whereCreatedAt($value)
 * @method static Builder|MultiBill whereId($value)
 * @method static Builder|MultiBill whereOpenid($value)
 * @method static Builder|MultiBill wherePayAmount($value)
 * @method static Builder|MultiBill wherePayAt($value)
 * @method static Builder|MultiBill wherePayNo($value)
 * @method static Builder|MultiBill wherePayServiceNo($value)
 * @method static Builder|MultiBill wherePayStatus($value)
 * @method static Builder|MultiBill wherePayWay($value)
 * @method static Builder|MultiBill whereTitle($value)
 * @method static Builder|MultiBill whereUpdatedAt($value)
 * @method static Builder|MultiBill whereUserId($value)
 * @mixin Eloquent
 */
class MultiBill extends BaseModel
{
    use BaseModelTrait;

    // 支付方式
    const PAY_WAY = [
        1 => '微信',
        2 => '支付宝',
    ];

    const PAY_WAY_ALIAS = [
        1 => 'wechat',
        2 => 'alipay',
    ];

    const BILL_STATUS = [
        0 => '未支付成功过',
        1 => '支付成功过',
    ];

    const PAY_STATUS = [
        1 => '未支付',
        2 => '付款成功',
        3 => '付款失败',
        4 => '付款取消',
        5 => '部分退款',
        6 => '全额退款',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $bill) {
            if (!$bill->pay_no) {
                $bill->pay_no = self::getNewNumber('', 'pay_no');
            }
            if (!$bill->bill_status) {
                $bill->bill_status = 0;
            }
            if (!$bill->pay_status) {
                $bill->pay_status = 1;
            }
        });
    }

    public function billable()
    {
        return $this->morphTo();
    }

    // 支付方式名称 pay_way_name
    public function getPayWayNameAttribute()
    {
        return self::PAY_WAY[$this->pay_way] ?? '';
    }

    // 支付方式别名 pay_way_alias
    public function getPayWayAliasAttribute()
    {
        return self::PAY_WAY_ALIAS[$this->pay_way] ?? '';
    }

    // 账单状态名称 bill_status_name
    public function getBillStatusNameAttribute()
    {
        return self::BILL_STATUS[$this->bill_status] ?? '';
    }

    // 支付状态名称 pay_status_name
    public function getPayStatusNameAttribute()
    {
        return self::PAY_STATUS[$this->pay_status] ?? '';
    }

    // 关联退款表
    public function refundBills()
    {
        return $this->hasMany(MultiRefundBill::class, 'multi_bill_id');
    }

    // 已退款的金额 refunded_amount
    public function getRefundedAmountAttribute()
    {
        return $this->refundBills->where('refund_status', 2)->sum('refund_amount');
    }

    // 退款中的金额 refunding_amount
    public function getRefundingAmountAttribute()
    {
        return $this->refundBills->where('refund_status', 1)->sum('refund_amount');
    }

    // 可退款的金额 can_refund_amount
    public function getCanRefundAmountAttribute()
    {
        return $this->pay_amount - $this->refunded_amount - $this->refunding_amount;
    }

    /**
     * 支付回调通知处理 - 异步.
     *
     * @param $data
     *
     * @return bool
     */
    public static function handleNotify($data, int $payWay)
    {
        /* @var self $bill */
        $bill = self::where('pay_no', $data->pay_no)->where('pay_way', $payWay)->first();
        if (!$bill) {
            pl('找不到支付订单信息：'.$data->pay_no, $bill->pay_way_alias.'-notify', 'pay');

            return true;
        }

        if (1 != $bill->pay_status) {
            // [3付款失败,4付款取消] 安排退款
            if (in_array($bill->pay_status, [3, 4])) {
                // todo
                return true;
            }
            pl('订单状态非未支付：'.$data->pay_no.'，订单状态：'.$bill->pay_status_name, $bill->pay_way_alias.'-notify-comment', 'pay');

            return true;
        }
        if ($bill->pay_amount != $data->pay_amount) {
            pl('订单支付金额不一致：'.$data->pay_no.'，订单金额：'.$bill->pay_amount.'，回调金额：'.$data->amount, $bill->pay_way_alias.'-notify-comment', 'pay');

            return false;
        }

        $bill->fill([
            'pay_service_no' => $data->pay_service_no,
            'pay_at' => now(),
            'bill_status' => 1,
            'pay_status' => 2,
        ]);

        $bill->save();

        event(new BillPayedEvent($bill));

        return true;
    }

    /**
     * 支付回调通知处理 - 同步.
     *
     * @param $payNo
     *
     * @return mixed
     */
    public static function handleReturn($payNo)
    {
        $bill = MultiBill::where('pay_no', $payNo)->first();
        if (!$bill) {
            abort(503, '订单不翼而飞了 :(');
        }

        if (method_exists($bill->billable, 'payResult')) {
            return $bill->billable->payResult();
        }

        return $bill->pay_status_name.':)';
    }

    /**
     * 退款操作.
     *
     * @param $refundAmount
     *
     * @return array|mixed
     *
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function toRefund($refundAmount)
    {
        $data = [
            'refund_amount' => $refundAmount,
        ];
        /* @var MultiRefundBill $refundBill */
        $refundBill = $this->refundBills()->create($data);
        switch ($this->pay_way) {
            case 1:
                $result = (new WechatPayService())->refund([
                    'pay_amount' => $this->pay_amount,
                    'refund_amount' => $refundBill->refund_amount,
                    'pay_no' => $this->pay_no,
                    'refund_no' => $refundBill->refund_no,
                ]);
                if (0 == $result['code']) {
                    if ($result['data']['refund_service_no'] ?? '') {
                        $refundBill->refund_service_no = $result['data']['refund_service_no'];
                    }
                    $refundBill->save();

                    return [
                        'code' => 0,
                        'msg' => $result['msg'],
                    ];
                }
                $refundBill->refund_status = 3;
                $refundBill->save();

                return $result;
                break;
            case 2:
                $result = (new AlipayService())->refund([
                    'refund_amount' => $refundBill->refund_amount,
                    'pay_no' => $this->pay_no,
                    'refund_no' => $refundBill->refund_no,
                ]);
                if (0 == $result['code']) {
                    $oldRefundBills = $this->refundBills()->get();
                    $sum = $oldRefundBills->count() > 0 ? $oldRefundBills->where('refund_status', 2)->sum('refund_amount') : 0;
                    if ($result['data']['refund_amount'] == $refundBill->refund_amount + $sum) {
                        if ($result['data']['refund_no'] ?? '') {
                            $refundBill->refund_no = $data['refund_no'];
                        }
                        if ($result['data']['refund_service_no'] ?? '') {
                            $refundBill->refund_service_no = $result['data']['refund_service_no'];
                        }
                        $refundBill->refund_at = $result['data']['refund_at'];
                        $refundBill->refund_status = 2;
                        $refundBill->save();
                        event(new BillRefundedEvent($refundBill));

                        return [
                            'code' => 0,
                            'msg' => $result['msg'],
                        ];
                    }
                    $refundBill->refund_status = 3;
                    $refundBill->save();

                    return [
                        'code' => 1,
                        'msg' => '退款金额不一致',
                        'service_code' => '',
                        'service_msg' => '',
                    ];
                }
                $refundBill->refund_status = 3;
                $refundBill->save();

                return $result;
                break;
        }
    }

    /**
     * 已有退款订单再次请求
     *
     * @param MultiRefundBill $refundBill
     *
     * @return array|mixed
     *
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function toRefundResend($refundBill)
    {
        $data = [
            'refund_amount' => $refundBill->refund_amount,
        ];
        switch ($this->pay_way) {
            case 1:
                $result = (new WechatPayService())->refund([
                    'pay_amount' => $this->pay_amount,
                    'refund_amount' => $refundBill->refund_amount,
                    'pay_no' => $this->pay_no,
                    'refund_no' => $refundBill->refund_no,
                ]);
                if (0 == $result['code']) {
                    if ($result['data']['refund_service_no'] ?? '') {
                        $refundBill->refund_service_no = $result['data']['refund_service_no'];
                    }
                    $refundBill->save();

                    return [
                        'code' => 0,
                        'msg' => $result['msg'],
                    ];
                }
                $refundBill->refund_status = 3;
                $refundBill->save();

                return $result;
                break;
            case 2:
                $refundBill = $this->refundBills()->create($data);
                $result = (new AlipayService())->refund([
                    'refund_amount' => $refundBill->refund_amount,
                    'pay_no' => $this->pay_no,
                    'refund_no' => $refundBill->refund_no,
                ]);
                if (0 == $result['code']) {
                    $oldRefundBills = $this->refundBills()->get();
                    $sum = $oldRefundBills->count() > 0 ? $oldRefundBills->where('refund_status', 2)->sum('refund_amount') : 0;
                    if ($result['data']['refund_amount'] == $refundBill->refund_amount + $sum) {
                        if ($result['data']['refund_no'] ?? '') {
                            $refundBill->refund_no = $data['refund_no'];
                        }
                        if ($result['data']['refund_service_no'] ?? '') {
                            $refundBill->refund_service_no = $result['data']['refund_service_no'];
                        }
                        $refundBill->refund_at = $result['data']['refund_at'];
                        $refundBill->refund_status = 2;
                        $refundBill->save();
                        event(new BillRefundedEvent($refundBill));

                        return [
                            'code' => 0,
                            'msg' => $result['msg'],
                        ];
                    }
                    $refundBill->refund_status = 3;
                    $refundBill->save();

                    return [
                        'code' => 1,
                        'msg' => '退款金额不一致',
                        'service_code' => '',
                        'service_msg' => '',
                    ];
                }
                $refundBill->refund_status = 3;
                $refundBill->save();

                return $result;
                break;
        }
    }

    /**
     * 订单支付检查.
     *
     * @return array
     *
     * @throws Exception
     */
    public function toPayFind()
    {
        DB::beginTransaction();
        try {
            switch ($this->pay_way) {
                case 1:
                    $wechatPayService = new WechatPayService();
                    $data = $wechatPayService->payFind($this->pay_no);
                    $result = $wechatPayService->payFindResultHandle($data);
                    if ($result) {
                        DB::commit();

                        return ['code' => 0, 'msg' => '订单检查已支付'];
                    }
                    DB::rollBack();

                    return ['code' => 1, 'msg' => '订单检查失败或未支付'];
                    break;
                case 2:
                    $alipayService = new AlipayService();
                    $data = $alipayService->payFind($this->pay_no);
                    $result = $alipayService->payFindResultHandle($data);
                    if ($result) {
                        DB::commit();

                        return ['code' => 0, 'msg' => '订单检查已支付'];
                    }
                    DB::rollBack();

                    return ['code' => 1, 'msg' => '订单检查失败或未支付'];
                    break;
            }
        } catch (Exception $e) {
            pl($this->pay_way_name.'支付订单'.$this->pay_no.'支付检查失败：'.$e->getMessage(), $this->pay_way_alias.'-pay-find-err', 'pay');
            DB::rollBack();

            return ['code' => 1, 'msg' => '订单支付失败'];
        }
    }
}
