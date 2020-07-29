<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('value')->nullable()->change();
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
