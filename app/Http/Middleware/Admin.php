<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class Admin
 *
 * @package App\Http\Middleware
 */
class Admin {
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request $request
	 * @param  Closure $next
	 *
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next) {
		if (!Auth::user()->admin) {
			if (str_contains($request->url(), '/api/') || $request->expectsJson() || $request->ajax()) {
				return new Response('Permission Denied', Response::HTTP_FORBIDDEN);
			}
			return redirect('/home');
		}

		return $next($request);
	}
}