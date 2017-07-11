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
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned()->nullable()->index('user_id');
			$table->string('bootswatch')->nullable()->default('cerulean');
			$table->nullableTimestamps();
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
