<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBudgetsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('budgets', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->index('user_id');
			$table->string('name')->default('No Name Budget');
			$table->decimal('income', 10)->nullable();
			$table->dateTime('start')->nullable();
			$table->dateTime('end')->nullable();
			$table->dateTime('created')->nullable();
			$table->dateTime('last_access')->nullable();
			$table->boolean('template')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('budgets');
	}
}
