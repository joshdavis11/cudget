<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBudgetCategoryRowExpensesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->foreign('budget_category_row_id', 'budget_category_row_expenses_ibfk_1')->references('id')->on('budget_category_rows')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('expense_id', 'budget_category_row_expenses_ibfk_2')->references('id')->on('expenses')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->dropForeign('budget_category_row_expenses_ibfk_1');
			$table->dropForeign('budget_category_row_expenses_ibfk_2');
		});
	}
}
