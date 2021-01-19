<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableBarbersAddcolumnIdPlan extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('barbers', function (Blueprint $table) {
			$table->unsignedInteger('plan_id')->nullable();
			$table->unsignedInteger('plan_old_id')->nullable();
			$table->timestamp('plan_due_date')->nullable();
			$table->timestamp('plan_old_due_date')->nullable();
			$table->foreign('plan_id')->references('id')->on('barbers');
			$table->foreign('plan_old_id')->references('id')->on('barbers');
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
			$table->dropForeign(['plan_id']);
			$table->dropForeign(['plan_old_id']);
		});
	}
}
