<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PreLoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ViewController;
use Illuminate\Routing\Router;

$router->get('/', ViewController::class . '@angular');
$router->get('login', ViewController::class . '@angular')->name('login');
$router->get('signup', ViewController::class . '@angular')->name('signup');
$router->post('login', PreLoginController::class . '@postLogin');
$router->get('password/reset', ForgotPasswordController::class . '@showLinkRequestForm')->name('password.request');
$router->post('password/email', ForgotPasswordController::class . '@sendResetLinkEmail')->name('password.email');
$router->get('password/reset/{token}', ResetPasswordController::class . '@showResetForm')->name('password.reset');
$router->post('password/reset', ResetPasswordController::class . '@reset');
$router->get('activate/new/{token}', ActivationController::class . '@new')->name('activation.new');
$router->get('activate/{token}', ActivationController::class . '@activate')->name('activation');
$router->get('403', ViewController::class . '@angular')->name('403');

$router->group(['middleware' => 'auth'], function(Router $router) {
	$router->get('logout', PreLoginController::class . '@logout')->name('logout');
	$router->get('home', ViewController::class . '@angular')->name('home');
	$router->get('import', ViewController::class . '@angular')->name('import');

	//Budgets
	$router->group(['prefix' => 'budgets'], function(Router $router) {
		$router->get('create/{id}', ViewController::class . '@angular');
		$router->get('{id}/edit', ViewController::class . '@angular');
		$router->get('{id}/share', ViewController::class . '@angular');
		$router->get('{id}', ViewController::class . '@angular');
		$router->get('', ViewController::class . '@angular');

		//Budget templates
		$router->group(['prefix' => 'templates'], function(Router $router) {
			$router->get('create/{id}', ViewController::class . '@angular');
			$router->get('{id}/edit', ViewController::class . '@angular');
			$router->get('{id}/share', ViewController::class . '@angular');
			$router->get('{id}', ViewController::class . '@angular');
			$router->get('', ViewController::class . '@angular');
		});
	});

	//Expenses
	$router->group(['prefix' => 'expenses'], function(Router $router) {
		$router->get('create', ViewController::class . '@angular');
		$router->get('', ViewController::class . '@angular');
	});

	//Income
	$router->group(['prefix' => 'income'], function(Router $router) {
		$router->get('create', ViewController::class . '@angular');
		$router->get('', ViewController::class . '@angular');
	});

	//Settings
	$router->group(['prefix' => 'settings'], function(Router $router) {
		$router->get('colors', ViewController::class . '@angular');
		$router->get('', ViewController::class . '@angular');

		//Settings users
		$router->group(['prefix' => 'users'], function(Router $router) {
			$router->get('create', ViewController::class . '@angular');
			$router->get('{id}/edit', ViewController::class . '@angular');
			$router->get('{id}/password', ViewController::class . '@angular');
			$router->get('{id}/permissions', ViewController::class . '@angular');
			$router->get('', ViewController::class . '@angular');
		});
	});

});

//Catch-all including 404
$router->any('{catchall}', ViewController::class . '@angular')->where('catchall', '^(?!.*[.].*$).*$');