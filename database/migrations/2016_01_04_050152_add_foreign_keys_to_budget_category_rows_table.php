<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBudgetCategoryRowsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('budget_category_rows', function (Blueprint $table) {
			$table->foreign('budget_category_id', 'budget_category_rows_ibfk_1')->references('id')->on('budget_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('budget_category_rows', function (Blueprint $table) {
			$table->dropForeign('budget_category_rows_ibfk_1');
		});
	}
}
