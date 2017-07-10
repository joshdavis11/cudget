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
		$router->get('perms', V1\PermissionsController::class . '@perms');
		$router->get('auth/user', V1\PermissionsController::class . '@authUser');

		//Budget routes
		$router->group(['prefix' => 'budgets'], function(Router $router) {
			$router->get('{id}/share', V1\BudgetsController::class. '@getShare');
			$router->post('{id}/share', V1\BudgetsController::class . '@postShare');
			$router->resource('income', V1\BudgetIncomeController::class);

			//Budget categories
			$router->group(['prefix' => 'categories'], function(Router $router) {

				//Budget category rows
				$router->group(['prefix' => 'rows'], function(Router $router) {
					$router->resource('expenses', V1\BudgetCategoryRowExpensesController::class);
				});
				$router->resource('rows', V1\BudgetCategoryRowsController::class);
				$router->get('rows/bcid/{id}', V1\BudgetCategoryRowsController::class . '@forBudgetCategory');
			});
			$router->resource('categories', V1\BudgetCategoriesController::class);
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

		//Import routes
		$router->group(['prefix' => 'import'], function(Router $router) {
			$router->resource('accounts', V1\AutoImportAccountController::class);
		});
		$router->post('import', V1\ImportController::class . '@import');

		//Settings routes
		$router->group(['prefix' => 'settings'], function(Router $router) {
			$router->put('configuration', V1\ConfigurationController::class . '@update');
		});

		//User routes
		$router->group(['prefix' => 'users'], function(Router $router) {
			$router->put('{id}/password', V1\UsersController::class . '@password');
			$router->resource('{userId}/permissions', V1\UserPermissionsController::class);
		});
		$router->resource('users', V1\UsersController::class);
	});
});

// /api/v1
$router->group(['prefix' => 'v1'], function(Router $router) {

	//Authentication
	$router->get('authenticated', PreLoginController::class . '@isAuthenticated');

	//Sign Up
	$router->post('signup', PreLoginController::class . '@signup');
});

//404
$router->any('{catchall}', function() {
	return new Response('', Response::HTTP_NOT_FOUND);
})->where('catchall', '(.*)');
