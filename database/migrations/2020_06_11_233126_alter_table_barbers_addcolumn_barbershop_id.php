<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbersAddcolumnBarbershopId extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('barbers', function (Blueprint $table) {
			$table->unsignedInteger('barbershop_id')->nullable();
			$table->foreign('barbershop_id')->references('id')->on('barbershops');		
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
			
		});
	}
}
