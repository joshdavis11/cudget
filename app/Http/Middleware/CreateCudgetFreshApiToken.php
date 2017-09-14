<?php

namespace App\Http\Middleware;

use Laravel\Passport\Http\Middleware\CreateFreshApiToken;

/**
 * Class CreateCudgetFreshApiToken
 *
 * @package App\Http\Middleware
 */
class CreateCudgetFreshApiToken extends CreateFreshApiToken {
	/**
	 * Determine if the request should receive a fresh token.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return bool
	 */
	protected function requestShouldReceiveFreshToken($request) {
		return ($request->isMethod('GET') || $request->ajax()) && $request->user($this->guard);
	}
}