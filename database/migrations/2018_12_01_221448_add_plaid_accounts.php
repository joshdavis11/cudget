<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddPlaidAccounts
 */
class AddPlaidAccounts extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('plaid_accounts', function(Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('plaid_data_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('account_id');
			$table->string('name');
			$table->smallInteger('mask')->unsigned();
			$table->string('type');
			$table->string('subtype');
			$table->boolean('include_in_updates')->default(true);
			$table->timestamps();
			$table->foreign('plaid_data_id', 'plaid_accounts_plaid_data_id')->references('id')->on('plaid_data')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('user_id', 'plaid_accounts_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('plaid_accounts');
	}
}
