<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsuarios extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('usuarios', function (Blueprint $table) {
      $table->increments('id');
      $table->string('nome', 200);
      $table->string('email', 100)->unique();
      $table->string('telefone', 15)->nullable();
      $table->string('senha');
      $table->string('url_imagem')->nullable();
      $table->boolean('ativo')->default(true);
      $table->softDeletes();
      $table->timestamps();
      });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('usuarios');
  }
}
