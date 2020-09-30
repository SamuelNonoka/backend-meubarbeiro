<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBarbershopsSchedulesDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barbershops_schedules_days', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('barbershop_id')->nullable();
            $table->unsignedInteger('schedule_day_id')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->boolean('open')->default(0);
            $table->timestamps();
            $table->softDeletes();
			$table->foreign('barbershop_id')->references('id')->on('barbershops');
			$table->foreign('schedule_day_id')->references('id')->on('schedules_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barbershops_schedules_days');
    }
}
