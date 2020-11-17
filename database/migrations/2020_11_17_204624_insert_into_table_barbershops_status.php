<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoTableBarbershopsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barbarshops_status', function (Blueprint $table) {
            DB::table('barbarshops_status')->insert(array('name' => 'Aguardando aprovação'));
            DB::table('barbarshops_status')->insert(array('name' => 'Ativada'));
            DB::table('barbarshops_status')->insert(array('name' => 'Bloqueada'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbarshops_status', function (Blueprint $table) {
            //
        });
    }
}
