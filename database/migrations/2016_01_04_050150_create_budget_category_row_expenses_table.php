<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBudgetCategoryRowExpensesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('budget_category_row_expenses', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('budget_category_row_id')->index('budget_category_row_id');
			$table->integer('expense_id')->nullable()->index('expense_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('budget_category_row_expenses');
	}
}
