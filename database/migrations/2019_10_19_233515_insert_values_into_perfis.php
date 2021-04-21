<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertValuesIntoPerfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $administrador  = array(
            "nome"  => "administrador",
            "ativo" => true
        );

        $proprietario    = array(
            "nome"  => "proprietario",
            "ativo" => true
        );

        $gerente        = array(
            "nome"  => "gerente",
            "ativo" => true
        );

        $barbeiro       = array(
            "nome"  => "barbeiro",
            "ativo" => true
        );

        $usuario        = array(
            "nome"  => "usuario",
            "ativo" => true
        );

        DB::table('perfis')->insert([$administrador, $proprietario, $gerente, $barbeiro, $usuario]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
