<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserLoginAttemptsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('user_login_attempts', function (Blueprint $table) {
			$table->foreign('user_id', 'user_login_attempts_ibfk_1')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('user_login_attempts', function (Blueprint $table) {
			$table->dropForeign('user_login_attempts_ibfk_1');
		});
	}
}
