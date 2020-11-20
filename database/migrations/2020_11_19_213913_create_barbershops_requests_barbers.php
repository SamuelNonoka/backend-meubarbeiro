<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarbershopsRequestsBarbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barbershops_requests_barbers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('barbershop_id')->nullable();
            $table->unsignedInteger('barber_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('barbershop_id')->references('id')->on('barbershops');
            $table->foreign('barber_id')->references('id')->on('barbers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barbershops_requests_barbers');
    }
}
