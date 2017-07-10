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

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PreLoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ViewController;
use Illuminate\Routing\Router;

$router->get('/', PreLoginController::class . '@preLogin');
$router->get('login', PreLoginController::class . '@preLogin')->name('login');
$router->get('signup', PreLoginController::class . '@preLogin')->name('signup');
$router->post('login', PreLoginController::class . '@postLogin');
$router->get('password/reset', ForgotPasswordController::class . '@showLinkRequestForm')->name('password.request');
$router->post('password/email', ForgotPasswordController::class . '@sendResetLinkEmail')->name('password.email');
$router->get('password/reset/{token}', ResetPasswordController::class . '@showResetForm')->name('password.reset');
$router->post('password/reset', ResetPasswordController::class . '@reset');

$router->group(['middleware' => 'auth'], function(Router $router) {
	$router->get('logout', PreLoginController::class . '@logout')->name('logout');
	$router->any('{catchall}', ViewController::class . '@angular')->where('catchall', '^(?!.*[.].*$).*$');
});