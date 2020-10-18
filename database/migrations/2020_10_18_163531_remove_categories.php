<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class RemoveCategories
 */
class RemoveCategories extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->dropForeign('expenses_ibfk_1');
			$table->dropColumn('expense_category_id');
		});
		Schema::table('income', function (Blueprint $table) {
			$table->dropForeign('income_ibfk_1');
			$table->dropColumn('income_category_id');
		});
		Schema::drop('expense_categories');
		Schema::drop('income_categories');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->integer('expense_category_id')->unsigned()->nullable()->index('expense_category_id');
			$table->foreign('expense_category_id', 'expenses_ibfk_1')->references('id')->on('expense_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
		Schema::table('income', function (Blueprint $table) {
			$table->integer('income_category_id')->unsigned()->nullable()->index('income_category_id');
			$table->foreign('income_category_id', 'income_ibfk_1')->references('id')->on('income_categories')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
		Schema::create('expense_categories', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->string('name');
			$table->nullableTimestamps();
		});
		Schema::create('income_categories', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->string('name');
			$table->nullableTimestamps();
		});
	}
}
