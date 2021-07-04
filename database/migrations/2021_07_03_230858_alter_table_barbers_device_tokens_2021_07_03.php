<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbersDeviceTokens20210703 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('barbers_device_tokens', 'token'))
        {
            Schema::table('barbers_device_tokens', function (Blueprint $table) {
                $table->renameColumn('token', 'device_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barbers_device_tokens', function (Blueprint $table) {
            //
        });
    }
}
