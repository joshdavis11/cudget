<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBudgetIncomeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('budget_income', function (Blueprint $table) {
			$table->foreign('budget_id', 'budget_income_ibfk_1')->references('id')->on('budgets')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('income_id', 'budget_income_ibfk_2')->references('id')->on('income')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('budget_income', function (Blueprint $table) {
			$table->dropForeign('budget_income_ibfk_1');
			$table->dropForeign('budget_income_ibfk_2');
		});
	}
}
