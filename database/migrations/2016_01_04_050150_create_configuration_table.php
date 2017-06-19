<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigurationTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('configuration', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->string('bootswatch')->nullable()->default('cerulean');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('configuration');
	}
}
