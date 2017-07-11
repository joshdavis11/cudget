<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExpensesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('expenses', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned()->index('expenses_ibfk_2');
			$table->dateTime('datetime')->nullable();
			$table->string('description')->default('No Name Expense');
			$table->integer('expense_category_id')->unsigned()->nullable()->index('expense_category_id');
			$table->decimal('amount', 10)->nullable();
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('expenses');
	}
}
