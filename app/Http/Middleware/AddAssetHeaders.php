<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class AddAssetHeaders
 *
 * @package App\Http\Middleware
 */
class AddAssetHeaders {
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request $request
	 * @param  Closure $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {
		$response = $next($request);
		if ($request->isXmlHttpRequest()) {
			$response->header('Cudget-app.js', mix('/js/app.js'));
			$response->header('Cudget-manifest.js', mix('/js/manifest.js'));
			$response->header('Cudget-vendor.js', mix('/js/vendor.js'));
		}

		return $response;
	}
}