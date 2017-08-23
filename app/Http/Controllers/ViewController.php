<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\View\View;

/**
 * Class ViewController
 *
 * @package App\Http\Controllers
 */
class ViewController extends Controller {
	/**
	 * Load the home page for vue to start working with
	 *
	 * @return View
	 */
	public function angular(SettingsService $SettingsService, UserService $UserService, AuthManager $Auth) {
		if ($Auth->user()) {
			return $this->postLogin($SettingsService, $UserService, $Auth);
		}

		return $this->preLogin();
	}

	public function postLogin(SettingsService $SettingsService, UserService $UserService, AuthManager $Auth) {
		$AuthUser = $Auth->user();
		$Configuration = $SettingsService->getConfigurationForUser($AuthUser->id);
		$data = [
			'bootswatch' => !empty($Configuration->bootswatch) ? $Configuration->bootswatch : 'flatly',
			'year' => date('Y'),
			'csrf' => csrf_token(),
			'AuthUser' => $AuthUser,
			'perms' => $UserService->getUserPermissions($AuthUser->id),
		];
		return view('home', $data);
	}

	/**
	 * Any route before login.
	 *
	 * @return View
	 */
	public function preLogin() {
		$data = [
			'year' => date('Y'),
			'csrf' => csrf_token()
		];
		return view('prelogin', $data);
	}
}