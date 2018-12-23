<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class ChangeMaskToString
 */
class ChangeMaskToString extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('plaid_accounts', function (Blueprint $table) {
			$table->string('mask', 5)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('plaid_accounts', function (Blueprint $table) {
			$table->smallInteger('mask')->unsigned()->change();
		});
	}
}
