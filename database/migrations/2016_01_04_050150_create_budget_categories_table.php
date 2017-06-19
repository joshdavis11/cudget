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
			$table->integer('id', true);
			$table->integer('budget_id')->index('budget_id');
			$table->string('name')->default('No Name Category');
			$table->integer('sort_order')->nullable();
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
