<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemoOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number')->index()->comment('订单号');
            $table->decimal('price')->comment('价格');
            $table->timestamp('pay_at')->comment('支付时间');
            $table->tinyInteger('status')->comment('状态(0未付款，1已付款)');
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
        Schema::dropIfExists('demo_orders');
    }
}
