<?php

namespace app\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ImportService;
use App\Utilities\Plaid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BankingController
 *
 * @package app\Http\Controllers\V1
 */
class BankingController extends Controller {
	/**
	 * update
	 *
	 * @param Request       $Request
	 * @param Plaid         $Plaid
	 * @param ImportService $ImportService
	 *
	 * @return Response
	 * @throws \Unirest\Exception
	 */
	public function update(Request $Request, Plaid $Plaid, ImportService $ImportService) {
		$userId = $Request->user()->id;
		$Budget = $ImportService->updateFromPlaid($userId);
		$headers = [];
		if (!empty($Budget->id)) {
			$headers['Location'] = '/budgets/' . $Budget->id;
		}

		return new Response('Updated!', Response::HTTP_OK, $headers);
	}
}