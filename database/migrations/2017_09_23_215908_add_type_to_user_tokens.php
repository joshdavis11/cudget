<?php

use App\Model\UserToken;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddTypeToUserTokens
 */
class AddTypeToUserTokens extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('user_tokens', function (Blueprint $table) {
			$table->string('type')->after('user_id');
		});

		DB::table('user_tokens')->update(['type' => UserToken::TYPE_ACTIVATION]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('user_tokens', function (Blueprint $table) {
			$table->dropColumn('type');
		});
	}
}
