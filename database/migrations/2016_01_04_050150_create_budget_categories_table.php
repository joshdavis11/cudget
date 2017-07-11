<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBudgetCategoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('budget_categories', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('budget_id')->unsigned()->index('budget_id');
			$table->string('name')->default('No Name Category');
			$table->integer('sort_order')->unsigned()->nullable();
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('budget_categories');
	}
}
