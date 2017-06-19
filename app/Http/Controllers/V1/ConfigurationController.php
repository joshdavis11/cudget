<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ConfigurationController
 *
 * @package App\Http\Controllers
 */
class ConfigurationController extends Controller {
	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request         $request
	 * @param AuthManager     $Auth
	 * @param SettingsService $SettingsService
	 *
	 * @return Response
	 */
	public function update(Request $request, AuthManager $Auth, SettingsService $SettingsService) {
		$SettingsService->updateConfiguration($Auth->user()->id, $request);
		return new Response('Updated');
	}
}
