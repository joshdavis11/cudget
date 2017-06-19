<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Response;

/**
 * Class PermissionsController
 *
 * @package App\Http\Controllers
 */
class PermissionsController extends Controller {
	/**
	 * Get perms for the authenticated user
	 *
	 * @param UserService $UserService
	 * @param AuthManager $Auth
	 *
	 * @return Response
	 */
	public function perms(UserService $UserService, AuthManager $Auth) {
		return new Response($UserService->getUserPermissions($Auth->user()->id));
	}

	/**
	 * Get the authenticated user
	 *
	 * @param AuthManager $Auth
	 *
	 * @return Response
	 */
	public function authUser(AuthManager $Auth) {
		return new Response($Auth->user());
	}
}
