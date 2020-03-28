<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class CatchAllController
 *
 * @package App\Http\Controllers\V1
 */
class CatchAllController extends Controller {
	/**
	 * catchAll
	 *
	 * @return Response
	 */
	public function catchAll(): Response {
		return new Response('', Response::HTTP_NOT_FOUND);
	}
}