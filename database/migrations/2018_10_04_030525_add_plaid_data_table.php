<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaidDataTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('plaid_data', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('access_token');
			$table->string('item_id');
			$table->datetime('last_run')->nullable();
			$table->timestamps();
			$table->foreign('user_id', 'plaid_data_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('plaid_data');
	}
}
