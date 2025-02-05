<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserPermissionsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('user_permissions', function (Blueprint $table) {
			$table->foreign('user_id', 'user_permissions_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('permission_id', 'user_permissions_permission_id')->references('id')->on('permissions')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('user_permissions', function (Blueprint $table) {
			$table->dropForeign('user_permissions_user_id');
		});
	}
}
