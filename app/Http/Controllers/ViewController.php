<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;

/**
 * Class ViewController
 *
 * @package App\Http\Controllers
 */
class ViewController extends Controller {
	/**
	 * Load the home page for vue to start working with
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function angular(SettingsService $SettingsService, UserService $UserService, AuthManager $Auth) {
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
}