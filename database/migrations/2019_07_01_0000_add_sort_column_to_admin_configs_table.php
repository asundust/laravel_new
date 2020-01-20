<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortColumnToAdminConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('admin.extensions.config.table', 'admin_config'), function (Blueprint $table) {
            $table->integer('sort')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('admin.extensions.config.table', 'admin_config'), function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
