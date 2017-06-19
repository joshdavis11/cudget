<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSharedBudgetsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('shared_budgets', function (Blueprint $table) {
			$table->foreign('budget_id', 'shared_budgets_ibfk_1')->references('id')->on('budgets')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'shared_budgets_ibfk_2')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('shared_budgets', function (Blueprint $table) {
			$table->dropForeign('shared_budgets_ibfk_1');
			$table->dropForeign('shared_budgets_ibfk_2');
		});
	}
}
