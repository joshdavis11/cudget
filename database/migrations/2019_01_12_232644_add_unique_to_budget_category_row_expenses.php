<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddUniqueToBudgetCategoryRowExpenses
 */
class AddUniqueToBudgetCategoryRowExpenses extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->unique('expense_id', 'bcre_expense_unique');
		});
		Schema::table('budget_income', function (Blueprint $table) {
			$table->unique('income_id', 'bi_income_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->dropUnique('bcre_expense_unique');
		});
//		Schema::table('budget_income', function (Blueprint $table) {
//			$table->dropUnique('bi_income_unique');
//		});
	}
}
