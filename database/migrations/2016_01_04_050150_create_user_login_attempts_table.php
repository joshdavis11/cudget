<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLoginAttemptsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('user_login_attempts', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->index('user_id');
			$table->string('ip')->nullable();
			$table->string('proxy')->nullable();
			$table->enum('status', array('failure', 'success'))->nullable();
			$table->dateTime('datetime')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('user_login_attempts');
	}
}
