<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiRefundBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_refund_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('multi_bill_id')->index()->comment('支付订单号id');
            $table->decimal('refund_amount', 12, 2)->comment('退款发起金额');
            $table->string('refund_no')->nullable()->default(null)->unique()->comment('退款商户订单号');
            $table->string('refund_service_no')->nullable()->default(null)->unique()->comment('退款支付商订单号');
            $table->timestamp('refund_at')->nullable()->default(null)->index()->comment('退款到账时间');
            $table->tinyInteger('refund_status')->index()->comment('退款状态(1退款中，2退款成功，3退款失败，4退款取消)');
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
        Schema::dropIfExists('multi_refund_bills');
    }
}
