<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddNewPerms
 */
class AddNewPerms extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		DB::table('permissions')->insert([
			['name' => 'Plaid Accounts', 'definition' => 'accounts', 'created_at' => Carbon::now(),],
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		DB::table('permissions')
		  ->where('definition', '=', 'accounts')
		  ->delete();
	}
}
