<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncomeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('income', function (Blueprint $table) {
			$table->foreign('income_category_id', 'income_ibfk_1')->references('id')->on('income_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id', 'income_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('income', function (Blueprint $table) {
			$table->dropForeign('income_ibfk_1');
			$table->dropForeign('income_ibfk_2');
		});
	}
}
