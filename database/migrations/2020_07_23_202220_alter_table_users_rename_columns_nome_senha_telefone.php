<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUsersRenameColumnsNomeSenhaTelefone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nome', 'name');
            $table->renameColumn('telefone', 'phone_number');
            $table->renameColumn('senha', 'password');
            $table->renameColumn('url_imagem', 'image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nome');
            $table->renameColumn('phone_number', 'telefone');
            $table->renameColumn('password', 'senha');
            $table->renameColumn('image_url', 'url_imagem');
        });
    }
}
