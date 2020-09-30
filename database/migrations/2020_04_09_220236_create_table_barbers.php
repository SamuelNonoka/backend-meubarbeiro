<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBarbers extends Migration
{
	public function up()
	{
		Schema::create('barbers', function (Blueprint $table) {
			$table->increments('id');
			$table->string('uuid', 100);
			$table->string('name', 50);
			$table->string('email', 50);
			$table->string('password', 200);
			$table->string('cpf', 11);
			$table->string('code', 4);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down() {
		Schema::dropIfExists('barbers');
	}
}
