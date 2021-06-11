<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoServicesType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services_type', function (Blueprint $table) {
            DB::table('services_types')->where('id', 1)->update(array('name' => 'Corte'));
            DB::table('services_types')->insert(array('name' => 'Combo'));
            DB::table('services_types')->insert(array('name' => 'Pintura'));
            DB::table('services_types')->insert(array('name' => 'Sobrancelha'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services_type', function (Blueprint $table) {
            //
        });
    }
}
