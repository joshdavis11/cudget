<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSharedBudgetsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('shared_budgets', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('budget_id')->index('budget_id');
			$table->integer('user_id')->index('user_id');
			$table->dateTime('shared_datetime')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('shared_budgets');
	}
}
