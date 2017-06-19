<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBudgetCategoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('budget_categories', function (Blueprint $table) {
			$table->foreign('budget_id', 'budget_categories_ibfk_1')->references('id')->on('budgets')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('budget_categories', function (Blueprint $table) {
			$table->dropForeign('budget_categories_ibfk_1');
		});
	}
}
