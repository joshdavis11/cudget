<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockedIpAddressesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('blocked_ip_addresses', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('ip')->nullable();
			$table->enum('status', array('temporary', 'permanent'))->nullable();
			$table->integer('blocked_minutes')->unsigned()->nullable();
			$table->dateTime('blocked_datetime')->nullable();
			$table->enum('reason', array('login'))->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('blocked_ip_addresses');
	}
}
