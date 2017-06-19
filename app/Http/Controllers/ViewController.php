<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
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
	public function angular(SettingsService $SettingsService, AuthManager $Auth) {
		$Configuration = $SettingsService->getConfigurationForUser($Auth->user()->id);
		$data = [
			'bootswatch' => !empty($Configuration->bootswatch) ? $Configuration->bootswatch : 'cerulean',
			'year' => date('Y'),
			'csrf' => csrf_token()
		];
		return view('home', $data);
	}
}