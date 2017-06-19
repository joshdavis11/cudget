<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBudgetIncomeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('budget_income', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('budget_id')->index('budget_id');
			$table->integer('income_id')->nullable()->index('income_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('budget_income');
	}
}
