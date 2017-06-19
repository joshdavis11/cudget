<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIncomeTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('income', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->index('income_ibfk_2');
			$table->dateTime('datetime')->nullable();
			$table->string('description')->default('No Name Expense');
			$table->integer('income_category_id')->nullable()->index('income_category_id');
			$table->decimal('amount', 10)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('income');
	}
}
