<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoScheduleStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules_status', function (Blueprint $table) {
            DB::table('schedules_status')->insert(array('name' => 'agendado'));
            DB::table('schedules_status')->insert(array('name' => 'cancelado'));
            DB::table('schedules_status')->insert(array('name' => 'aguardando'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules_status', function (Blueprint $table) {
            //
        });
    }
}
