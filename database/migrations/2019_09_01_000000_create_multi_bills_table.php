<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('billable_type')->index()->comment('多态模型名称');
            $table->string('billable_id')->nullable()->default(null)->index()->comment('多态模型id');
            $table->integer('user_id')->nullable()->default(null)->index()->comment('用户id');
            $table->string('openid')->nullable()->default(null)->index()->comment('Openid(微信支付涉及)');
            $table->decimal('amount', 12, 2)->comment('支付发起金额');
            $table->string('pay_no')->unique()->comment('商户订单号');
            $table->string('pay_service_no')->nullable()->default(null)->unique()->comment('支付商订单号');
            $table->timestamp('pay_at')->nullable()->default(null)->unique()->comment('支付成功时间');
            $table->string('refund_no')->nullable()->default(null)->unique()->comment('退款商户订单号');
            $table->string('refund_service_no')->nullable()->default(null)->unique()->comment('退款支付商订单号');
            $table->timestamp('refund_at')->nullable()->default(null)->unique()->comment('退款到账时间');
            $table->tinyInteger('pay_way')->nullable()->default(null)->index()->comment('支付方式(1微信，2支付宝)');
            $table->tinyInteger('status')->index()->comment('状态(1未支付，2付款成功，3付款失败，4付款取消，5退款中，6退款成功，7退款失败)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multi_bills');
    }
}
