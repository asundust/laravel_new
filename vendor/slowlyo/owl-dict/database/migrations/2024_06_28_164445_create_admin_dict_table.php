<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('admin_dict', function (Blueprint $table) {
            $table->string('extension')->nullable()->comment('扩展');
        });
    }

    public function down()
    {
        Schema::table('admin_dict', function (Blueprint $table) {
            $table->removeColumn('extension');
        });
    }
};
