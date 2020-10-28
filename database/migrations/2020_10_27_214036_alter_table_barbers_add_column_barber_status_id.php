<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbersAddColumnBarberStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barbers', function (Blueprint $table) {
            $table->unsignedBigInteger('barber_status_id')->default(1);
            $table->foreign('barber_status_id')->references('id')->on('barbers_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbers', function (Blueprint $table) {
            //
        });
    }
}
