<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBarbersDeviceTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barbers_device_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('barber_id');
            $table->string('token');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('barbers_device_tokens');
    }
}
