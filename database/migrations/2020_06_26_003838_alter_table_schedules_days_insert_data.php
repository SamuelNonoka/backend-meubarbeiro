<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSchedulesDaysInsertData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('schedules_days')->insert(array(
            'name'       => 'Segunda',
            'short_name' => 'Seg'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Terça',
            'short_name' => 'Ter'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Quarta',
            'short_name' => 'Qua'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Quinta',
            'short_name' => 'Qui'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Sexta',
            'short_name' => 'Sex'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Sábado',
            'short_name' => 'Sab'
        ));
        DB::table('schedules_days')->insert(array(
            'name'       => 'Domingo',
            'short_name' => 'Dom'
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules_days', function (Blueprint $table) {
            //
        });
    }
}
