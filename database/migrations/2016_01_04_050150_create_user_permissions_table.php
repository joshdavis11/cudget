<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPermissionsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('user_permissions', function (Blueprint $table) {
			$table->integer('id', true);
			$table->integer('user_id');
			$table->string('section', 100);
			$table->enum('permission', array('add', 'delete', 'update', 'view'));
			$table->boolean('access')->nullable()->default(0);
			$table->unique(['user_id', 'section', 'permission'], 'user_section_permission');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('user_permissions');
	}
}
