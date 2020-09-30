<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServicesTypes extends Migration
{
	public function up()
	{
		Schema::create('services_types', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 20);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down() {
		Schema::dropIfExists('services_types');
	}
}
