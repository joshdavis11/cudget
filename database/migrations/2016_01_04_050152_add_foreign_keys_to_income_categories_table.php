<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncomeCategoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('income_categories', function (Blueprint $table) {
			$table->foreign('user_id', 'income_categories_ibfk_1')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('income_categories', function (Blueprint $table) {
			$table->dropForeign('income_categories_ibfk_1');
		});
	}
}
