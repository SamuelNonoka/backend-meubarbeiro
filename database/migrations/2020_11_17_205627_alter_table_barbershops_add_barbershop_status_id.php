<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbershopsAddBarbershopStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barbershops', function (Blueprint $table) {
            $table->unsignedBigInteger('barbershop_status_id')->default(1);
            $table->foreign('barbershop_status_id')->references('id')->on('barbershops_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('barbershops_status');
        Schema::table('barbershops', function (Blueprint $table) {
            //$table->dropForeign(['barbershop_status_id']);
            //$table->dropColumn('barbershop_status_id');
        });
    }
}
