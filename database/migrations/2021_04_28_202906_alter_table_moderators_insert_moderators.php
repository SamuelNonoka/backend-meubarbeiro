<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Services\CryptService;

class AlterTableModeratorsInsertModerators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('moderators', function (Blueprint $table) {
            DB::table('moderators')->insert(array('name' => 'Samuel', 'password' => CryptService::encrypt('5*#}xxOJ')));
            DB::table('moderators')->insert(array('name' => 'Dayana', 'password' => CryptService::encrypt('5*#}xxOJ')));
            DB::table('moderators')->insert(array('name' => 'Jéssika', 'password' => CryptService::encrypt('5*#}xxOJ')));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moderators', function (Blueprint $table) {
            //
        });
    }
}
