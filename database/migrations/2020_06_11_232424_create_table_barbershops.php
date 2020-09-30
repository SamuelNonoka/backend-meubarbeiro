<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBarbershops extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('barbershops', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('address_id')->nullable();
			$table->unsignedInteger('admin_id')->nullable();
			$table->string('name', 50);
			$table->text('image_url');
			$table->string('description', 200);
			$table->string('phone_number', 11);
			$table->text('instagram_url');
			$table->text('facebook_url');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('address_id')->references('id')->on('addresses');
			$table->foreign('admin_id')->references('id')->on('barbers');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('barbershops');
	}
}
