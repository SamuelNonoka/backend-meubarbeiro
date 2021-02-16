<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbershopsAddBarbershopId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('barbershops', 'barbershop_status_id'))
        {
            Schema::table('barbershops', function (Blueprint $table) {
                $table->unsignedInteger('barbershop_status_id')->nullable();
                $table->foreign('barbershop_status_id')->references('id')->on('barbershops_status');
            });   
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbershops', function (Blueprint $table) {
            //
        });
    }
}
