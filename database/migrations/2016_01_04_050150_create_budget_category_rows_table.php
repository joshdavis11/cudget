<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBudgetCategoryRowsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('budget_category_rows', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('budget_category_id')->index('budget_category_id');
			$table->string('name')->default('No Name Row');
			$table->decimal('estimated', 10)->nullable();
			$table->integer('sort_order')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('budget_category_rows');
	}
}
