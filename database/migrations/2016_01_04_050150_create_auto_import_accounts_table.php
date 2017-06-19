<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAutoImportAccountsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('auto_import_accounts', function (Blueprint $table) {
			$table->integer('id', true);
			$table->string('name');
			$table->integer('description');
			$table->integer('date');
			$table->integer('amount');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('auto_import_accounts');
	}
}
