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
            $table->id();
            $table->string('billable_type')->index()->comment('多态模型名称');
            $table->string('billable_id')->nullable()->default(null)->index()->comment('多态模型id');
            $table->integer('user_id')->nullable()->default(null)->index()->comment('用户id');
            $table->string('openid')->nullable()->default(null)->index()->comment('Openid(微信支付涉及)');
            $table->string('title')->nullable()->default(null)->comment('订单名称');
            $table->tinyInteger('pay_way')->nullable()->default(null)->index()->comment('支付方式(1微信，2支付宝)');
            $table->decimal('pay_amount', 12, 2)->comment('支付发起金额');
            $table->string('pay_no')->unique()->comment('商户订单号');
            $table->string('pay_service_no')->nullable()->default(null)->unique()->comment('支付商订单号');
            $table->timestamp('pay_at')->nullable()->default(null)->index()->comment('支付成功时间');
            $table->tinyInteger('bill_status')->index()->comment('账单状态(0未支付成功过，1支付成功过)');
            $table->tinyInteger('pay_status')->index()->comment('支付状态(1未支付，2付款成功，3付款失败，4付款取消，5部分退款，6全额退款)');
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
