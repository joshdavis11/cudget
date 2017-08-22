<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTokensTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('user_tokens', function (Blueprint $table) {
			$table->integer('id', true)->unsigned();
			$table->integer('user_id')->unsigned();
			$table->string('token');
			$table->timestamp('expires')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamps();
			$table->foreign('user_id', 'user_tokens_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('user_tokens');
	}
}
