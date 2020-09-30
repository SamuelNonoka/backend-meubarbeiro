<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableServices extends Migration
{
	public function up()
	{
		Schema::create('services', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('service_type_id')->nullable();
			$table->string('name', 50);
			$table->decimal('price', 8, 2);
			$table->time('duration_time');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('service_type_id')->references('id')->on('services_types');
		});
	}

	public function down() {
		Schema::dropIfExists('services');
	}
}
