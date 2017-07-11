<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToExpensesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->foreign('expense_category_id', 'expenses_ibfk_1')->references('id')->on('expense_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'expenses_ibfk_2')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->dropForeign('expenses_ibfk_1');
			$table->dropForeign('expenses_ibfk_2');
		});
	}
}
