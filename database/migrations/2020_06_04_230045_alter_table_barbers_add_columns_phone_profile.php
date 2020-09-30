<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbersAddColumnsPhoneProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barbers', function (Blueprint $table) {
            $table->string('profile', 200)->nullable();
            $table->string('phone', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbers', function (Blueprint $table) {
            //
        });
    }
}
