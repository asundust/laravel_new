<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::create('admin_dict', function (Blueprint $table) {
            $table->comment('数据字典');
            $table->id();

            $table->string('parent_id')->default(0)->comment('父级ID')->index();
            $table->string('key')->comment('编码')->index();
            $table->tinyInteger('enabled')->default(1)->comment('是否启用')->index();
            $table->integer('sort')->default(0)->comment('排序')->index();
            $table->text('value')->comment('名称');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_dict');
    }
};
