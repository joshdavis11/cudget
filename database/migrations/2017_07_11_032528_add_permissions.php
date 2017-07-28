<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddPermissions
 */
class AddPermissions extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		DB::table('permissions')->insert([
			['name' => 'Import', 'definition' => 'import', 'created_at' => Carbon::now(),],
			['name' => 'Budget Templates', 'definition' => 'budgetTemplates', 'created_at' => Carbon::now(),],
			['name' => 'Color Scheme', 'definition' => 'colorScheme', 'created_at' => Carbon::now(),],
			['name' => 'Budget Sharing', 'definition' => 'budgetTemplates', 'created_at' => Carbon::now(),],
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		DB::table('permissions')
		  ->where('definition', '=', 'import')
		  ->orWhere('definition', '=', 'budgetTemplates')
		  ->orWhere('definition', '=', 'colorScheme')
		  ->orWhere('definition', '=', 'budgetTemplates')
		  ->delete();
	}
}
