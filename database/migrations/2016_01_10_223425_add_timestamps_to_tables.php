<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToTables extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('auto_import_accounts', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('blocked_ip_addresses', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('budget_categories', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('budget_category_rows', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('budget_income', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('budgets', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('configuration', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('expense_categories', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('expenses', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('income', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('income_categories', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('shared_budgets', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
		Schema::table('user_permissions', function (Blueprint $table) {
			$table->nullableTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('auto_import_accounts', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('blocked_ip_addresses', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('budget_categories', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('budget_category_rows', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('budget_category_row_expenses', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('budget_income', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('budgets', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('configuration', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('expense_categories', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('expenses', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('income', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('income_categories', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('shared_budgets', function (Blueprint $table) {
			$table->dropTimestamps();
		});
		Schema::table('user_permissions', function (Blueprint $table) {
			$table->dropTimestamps();
		});
	}
}
