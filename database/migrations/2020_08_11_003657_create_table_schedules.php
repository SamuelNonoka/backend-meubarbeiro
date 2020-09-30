<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('barbershop_id')->nullable();
            $table->unsignedInteger('barber_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('schedule_status_id')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('barbershop_id')->references('id')->on('barbershops');
            $table->foreign('barber_id')->references('id')->on('barbers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('schedule_status_id')->references('id')->on('schedules_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
