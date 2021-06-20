<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoSchedulesStatus20210619 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules_status', function (Blueprint $table) {
            DB::table('schedules_status')->insert(array('name' => 'Atendido'));
            DB::table('schedules_status')->insert(array('name' => 'Sem resposta do  barbeiro'));
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
