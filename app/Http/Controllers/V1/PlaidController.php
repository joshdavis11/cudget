<?php

namespace App\Http\Controllers\V1;

use app\Exceptions\PlaidAccessTokenException;
use App\Exceptions\PlaidRequestException;
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
		} catch(PlaidRequestException $exception) {
			return new Response($exception->getErrorMessage(), $exception->getCode());
		}
		$PlaidData = new PlaidData();
		$PlaidData->userId = $Request->user()->id;
		$PlaidData->accessToken = $PlaidAccessToken->getAccessToken();
		$PlaidData->itemId = $PlaidAccessToken->getItemId();
		$PlaidData->save();

		return new Response('Created', Response::HTTP_CREATED);
	}

	/**
	 * requestPublicToken
	 *
	 * @param Request $Request
	 * @param int     $plaidDataId
	 * @param Plaid   $Plaid
	 *
	 * @return Response
	 */
	public function requestPublicToken(Request $Request, int $plaidDataId, Plaid $Plaid) {
		$PlaidData = PlaidData::findOrFail($plaidDataId);
		if ($Request->user()->id !== $PlaidData->userId) {
			return new Response('Forbidden', Response::HTTP_FORBIDDEN);
		}

		try {
			$publicToken = $Plaid->getPublicToken($PlaidData->accessToken);
		} catch(Exception $exception) {
			return new Response('Error making the request', Response::HTTP_INTERNAL_SERVER_ERROR);
		} catch(PlaidRequestException $exception) {
			return new Response($exception->getErrorMessage(), $exception->getCode());
		}

		return new Response($publicToken);
	}

	/**
	 * getAccounts
	 *
	 * @param Plaid $Plaid
	 *
	 * @return void
	 * @throws Exception
	 */
	public function getAccounts(Plaid $Plaid) {
		$Plaid->getAuth();
	}
}