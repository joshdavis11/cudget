<?php

namespace App\Http\Controllers\V1;

use app\Exceptions\PlaidAccessTokenException;
use App\Http\Controllers\Controller;
use App\Model\PlaidData;
use App\Utilities\Plaid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Unirest\Exception;

/**
 * Class PlaidController
 *
 * @package App\Http\Controllers\V1
 */
class PlaidController extends Controller {
	/**
	 * requestAccessToken
	 *
	 * @param Request $Request
	 * @param Plaid   $Plaid
	 *
	 * @return Response
	 */
	public function requestAccessToken(Request $Request, Plaid $Plaid) {
		$publicToken = $Request->get('publicToken');
		try {
			$PlaidAccessToken = $Plaid->getAccessToken($publicToken);
		} catch(Exception $exception) {
			return new Response('Error making the request', Response::HTTP_INTERNAL_SERVER_ERROR);
		} catch(PlaidAccessTokenException $plaidAccessTokenException) {
			return new Response("Response didn't contain the access token", Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		$PlaidData = new PlaidData();
		$PlaidData->userId = $Request->user()->id;
		$PlaidData->accessToken = $PlaidAccessToken->getAccessToken();
		$PlaidData->itemId = $PlaidAccessToken->getItemId();
		$PlaidData->save();

		return new Response('Created', Response::HTTP_CREATED);
	}

	public function getAccounts(Plaid $Plaid) {
		$Plaid->getAuth();
	}
}