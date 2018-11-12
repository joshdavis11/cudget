<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class UpdateExpenses
 */
class UpdateExpenses extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->string('iso_currency_code')->nullable()->default('USD')->after('amount');
			$table->string('transaction_id')->nullable()->after('iso_currency_code');
			$table->string('account_id')->nullable()->after('transaction_id');
			$table->unique(['user_id', 'transaction_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('expenses', function (Blueprint $table) {
			$table->dropUnique('expenses_user_id_transaction_id_unique');
			$table->dropColumn('iso_currency_code');
			$table->dropColumn('transaction_id');
			$table->dropColumn('account_id');
		});
	}
}
