<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSchedulesStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alter_table_schedules_status', function (Blueprint $table) {
            DB::table('schedules_status')->where('id', 4)->update(array('name' => 'Cancelado pelo barbeiro'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alter_table_schedules_status', function (Blueprint $table) {
            //
        });
    }
}
