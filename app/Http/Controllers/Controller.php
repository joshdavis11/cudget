<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController {
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Get the error processing message
	 *
	 * @return string
	 */
	protected function errorProcessingMessage() {
		return 'There was an error processing your request';
	}

	/**
	 * Get the authorization message
	 *
	 * @return string
	 */
	protected function authorizationMessage() {
		return 'Permission Denied';
	}
}
