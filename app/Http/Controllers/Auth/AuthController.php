<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller {
	/**
	 * Get the CSRF token
	 *
	 * @return Response
	 */
    public function getCSRF(): Response {
		return new Response(['csrf' => csrf_token()]);
	}
}