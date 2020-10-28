<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoBarbersStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barbers_status', function (Blueprint $table) {
            DB::table('barbers_status')->insert(array('name' => 'Aguardando cadastro'));
            DB::table('barbers_status')->insert(array('name' => 'Ativo'));
            DB::table('barbers_status')->insert(array('name' => 'Bloqueado'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbers_status', function (Blueprint $table) {
            //
        });
    }
}
