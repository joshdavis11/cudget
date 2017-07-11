<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToExpenseCategoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('expense_categories', function (Blueprint $table) {
			$table->foreign('user_id', 'expense_categories_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('expense_categories', function (Blueprint $table) {
			$table->dropForeign('expense_categories_ibfk_1');
		});
	}
}
