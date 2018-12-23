<?php

use App\Http\Controllers\Auth\PreLoginController;
use App\Http\Controllers\V1;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// /api
$router->group(['middleware' => 'auth:api'], function(Router $router) {

    // /api/v1
	$router->group(['prefix' => 'v1'], function(Router $router) {

		//Perms
		$router->group(['middleware' => 'admin'], function(Router $router) {
			$router->resource('permissions', V1\PermissionsController::class);
		});
		$router->get('myperms', V1\UserPermissionsController::class . '@perms');
		$router->get('auth/user', V1\UserPermissionsController::class . '@authUser');

		//Budget routes
		$router->group(['prefix' => 'budgets'], function(Router $router) {
			$router->get('{id}/share', V1\BudgetsController::class. '@getShare');
			$router->post('{id}/share', V1\BudgetsController::class . '@postShare');
			$router->get('{id}/expenses', V1\BudgetsController::class . '@getExpensesForBudget');
			$router->get('{id}/income', V1\BudgetsController::class . '@getIncomeForBudget');
			$router->resource('income', V1\BudgetIncomeController::class,
				[
					'names' => [
						'create' => 'budgetIncome.create',
						'store' => 'budgetIncome.store',
						'show' => 'budgetIncome.show',
						'index' => 'budgetIncome.index',
						'update' => 'budgetIncome.update',
						'destroy' => 'budgetIncome.destroy',
						'edit' => 'budgetIncome.edit',
					],
				]
			);

			//Budget categories
			$router->group(['prefix' => 'categories'], function(Router $router) {

				//Budget category rows
				$router->group(['prefix' => 'rows'], function(Router $router) {
					$router->put('expenses/bulk', V1\BudgetCategoryRowExpensesController::class . '@bulkUpdate');
					$router->resource('expenses', V1\BudgetCategoryRowExpensesController::class,
						[
							'names' => [
								'create' => 'budgetCategoryRowExpense.create',
								'store' => 'budgetCategoryRowExpense.store',
								'show' => 'budgetCategoryRowExpense.show',
								'index' => 'budgetCategoryRowExpense.index',
								'update' => 'budgetCategoryRowExpense.update',
								'destroy' => 'budgetCategoryRowExpense.destroy',
								'edit' => 'budgetCategoryRowExpense.edit',
							],
						]
					);
				});
				$router->resource('rows', V1\BudgetCategoryRowsController::class,
					[
						'names' => [
							'create' => 'budgetCategoryRow.create',
							'store' => 'budgetCategoryRow.store',
							'show' => 'budgetCategoryRow.show',
							'index' => 'budgetCategoryRow.index',
							'update' => 'budgetCategoryRow.update',
							'destroy' => 'budgetCategoryRow.destroy',
							'edit' => 'budgetCategoryRow.edit',
						],
					]
				);
				$router->get('rows/bcid/{id}', V1\BudgetCategoryRowsController::class . '@forBudgetCategory');
			});
			$router->resource('categories', V1\BudgetCategoriesController::class,
				[
					'names' => [
						'create' => 'budgetCategory.create',
						'store' => 'budgetCategory.store',
						'show' => 'budgetCategory.show',
						'index' => 'budgetCategory.index',
						'update' => 'budgetCategory.update',
						'destroy' => 'budgetCategory.destroy',
						'edit' => 'budgetCategory.edit',
					],
				]
			);
			$router->get('categories/bid/{id}', V1\BudgetCategoriesController::class . '@forBudget');

			//Templates
			$router->group(['prefix' => 'templates'], function(Router $router) {
				$router->get('{id}/share', V1\BudgetTemplatesController::class . '@getShare');
				$router->post('{id}/share', V1\BudgetTemplatesController::class . '@postShare');
			});
			$router->resource('templates', V1\BudgetTemplatesController::class);
		});
		$router->resource('budgets', V1\BudgetsController::class);

		//Expense routes
		$router->group(['prefix' => 'expenses'], function(Router $router) {
			$router->resource('categories', V1\ExpenseCategoriesController::class);
		});
		$router->resource('expenses', V1\ExpenseController::class);

		//Income routes
		$router->group(['prefix' => 'income'], function(Router $router) {
			$router->resource('categories', V1\IncomeCategoriesController::class);
		});
		$router->resource('income', V1\IncomeController::class);

		//banking routes
		$router->group(['prefix' => 'banking'], function(Router $router) {
			$router->post('connect', V1\PlaidController::class . '@requestAccessToken')->name('banking.accessToken');
			$router->get('publicToken/{plaidDataId}', V1\PlaidController::class . '@requestPublicToken')->name('banking.publicToken');
			$router->get('accounts', V1\PlaidController::class . '@getAccounts')->name('banking.accounts');
			$router->post('accounts/{id}', V1\PlaidController::class . '@updateAccount')->name('banking.account.update');
			$router->group(['prefix' => 'import'], function(Router $router) {
				$router->resource('accounts', V1\AutoImportAccountController::class);
			});
			$router->post('import', V1\ImportController::class . '@import');
			$router->post('update', V1\BankingController::class . '@update');
		});

		//Settings routes
		$router->group(['prefix' => 'settings'], function(Router $router) {
			$router->put('configuration', V1\ConfigurationController::class . '@update');
		});

		//User routes
		$router->group(['middleware' => 'admin'], function(Router $router) {
			$router->group(['prefix' => 'users'], function(Router $router) {
				$router->put('{id}/password', V1\UsersController::class . '@password');
				$router->resource('{userId}/permissions', V1\UserPermissionsController::class);
			});
			$router->resource('users', V1\UsersController::class);
		});
	});
});

// /api/v1
$router->group(['prefix' => 'v1'], function(Router $router) {
	//Sign Up
	$router->post('signup', PreLoginController::class . '@signup');
	$router->get('users/emailExists/{email}', V1\UsersController::class . '@emailExists');
	$router->get('users/usernameExists/{username}', V1\UsersController::class . '@usernameExists');
});

//404
$router->any('{catchall}', function() {
	return new Response('', Response::HTTP_NOT_FOUND);
})->where('catchall', '(.*)');
