<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddInstitutionToPlaidData
 */
class AddInstitutionToPlaidData extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('plaid_data', function (Blueprint $table) {
			$table->string('institution_id')->after('item_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('plaid_data', function (Blueprint $table) {
			$table->dropColumn('institution_id');
		});
	}
}
