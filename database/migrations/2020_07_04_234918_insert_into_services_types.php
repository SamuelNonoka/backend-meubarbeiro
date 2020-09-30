<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertIntoServicesTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services_types', function (Blueprint $table) {
            DB::table('services_types')->insert(array('name' => 'Cabelo'));
            DB::table('services_types')->insert(array('name' => 'Barba'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services_types', function (Blueprint $table) {
            //
        });
    }
}
