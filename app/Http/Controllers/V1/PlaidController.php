<?php

namespace App\Http\Controllers\V1;

use app\Exceptions\PlaidAccessTokenException;
use App\Exceptions\PlaidRequestException;
use App\Http\Controllers\Controller;
use App\Model\Permission;
use App\Model\PlaidAccount;
use App\Model\PlaidData;
use App\Model\PlaidWebhook;
use App\Services\PermissionsService;
use App\Services\UserService;
use App\Utilities\Plaid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Unirest\Exception;

/**
 * Class PlaidController
 *
 * @package App\Http\Controllers\V1
 */
class PlaidController extends Controller {
	const PLAID_IPS = ['52.21.26.131', '52.21.47.157', '52.41.247.19', '52.88.82.239'];

	/**
	 * requestAccessToken
	 *
	 * @param Request            $Request
	 * @param Plaid              $Plaid
	 * @param PermissionsService $PermissionsService
	 * @param UserService        $UserService
	 *
	 * @return Response
	 */
	public function requestAccessToken(Request $Request, Plaid $Plaid, PermissionsService $PermissionsService, UserService $UserService) {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_ACCOUNTS);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

		$publicToken = $Request->get('publicToken');
		$metadata = $Request->get('metadata');
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
		$PlaidData->userId = $userId;
		$PlaidData->accessToken = $PlaidAccessToken->getAccessToken();
		$PlaidData->institutionId = $metadata['institution']['institution_id'];
		$PlaidData->itemId = $PlaidAccessToken->getItemId();
		$PlaidData->save();

		foreach($metadata['accounts'] ?? [] as $account) {
			$PlaidAccount = new PlaidAccount();
			$PlaidAccount->plaidDataId = $PlaidData->id;
			$PlaidAccount->userId = $userId;
			$PlaidAccount->accountId = $account['id'];
			$PlaidAccount->name = $account['name'];
			$PlaidAccount->mask = $account['mask'];
			$PlaidAccount->type = $account['type'];
			$PlaidAccount->subtype = $account['subtype'];
			$PlaidAccount->includeInUpdates = true;
			$PlaidAccount->save();
		}

		return new Response('Created', Response::HTTP_CREATED);
	}

	/**
	 * requestPublicToken
	 *
	 * @param Request            $Request
	 * @param int                $plaidDataId
	 * @param Plaid              $Plaid
	 * @param PermissionsService $PermissionsService
	 * @param UserService        $UserService
	 *
	 * @return Response
	 */
	public function requestPublicToken(Request $Request, int $plaidDataId, Plaid $Plaid, PermissionsService $PermissionsService, UserService $UserService) {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_ACCOUNTS);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

		$PlaidData = PlaidData::findOrFail($plaidDataId);
		if($userId !== $PlaidData->userId) {
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
	 * Plaid webhooks
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function webhook(Request $request) {
		if (App::environment('production') && !in_array($request->ip(), self::PLAID_IPS)) {
			return new Response(null, Response::HTTP_NOT_FOUND);
		}

		PlaidWebhook::create(['data' => json_encode($request->all())]);

		return new Response();
	}
}