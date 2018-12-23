<?php

namespace App\Http\Controllers\V1;

use app\Exceptions\PlaidAccessTokenException;
use App\Exceptions\PlaidRequestException;
use App\Http\Controllers\Controller;
use App\Model\PlaidAccount;
use App\Model\PlaidData;
use App\Utilities\Plaid;
use Illuminate\Database\Eloquent\Collection;
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

		$userId = $Request->user()->id;

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
	 * @param Request $Request
	 * @param int     $plaidDataId
	 * @param Plaid   $Plaid
	 *
	 * @return Response
	 */
	public function requestPublicToken(Request $Request, int $plaidDataId, Plaid $Plaid) {
		$PlaidData = PlaidData::findOrFail($plaidDataId);
		if($Request->user()->id !== $PlaidData->userId) {
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
	 * @return Response
	 */
	public function getAccounts(Request $Request, Plaid $Plaid): Response {
		$userId = $Request->user()->id;
		$PlaidDataRecords = PlaidData::where('user_id', '=', $userId)->get();

		$AllPlaidAccounts = [];
		foreach($PlaidDataRecords as $PlaidData) {
			/** @var PlaidData $PlaidData */
			$PlaidAccounts = PlaidAccount::where('plaid_data_id', '=', $PlaidData->id)->get();
			/** @var Collection $PlaidAccounts */
			if($PlaidAccounts->isEmpty()) {
				try {
					$Accounts = $Plaid->getAuth($PlaidData->accessToken);
				} catch(Exception $exception) {
					return new Response('Error making the request', Response::HTTP_INTERNAL_SERVER_ERROR);
				} catch(PlaidRequestException $exception) {
					return new Response($exception->getErrorMessage(), $exception->getCode());
				}
				foreach($Accounts as $Account) {
					$PlaidAccount = new PlaidAccount();
					$PlaidAccount->plaidDataId = $PlaidData->id;
					$PlaidAccount->userId = $userId;
					$PlaidAccount->accountId = $Account->getAccountId();
					$PlaidAccount->name = $Account->getName();
					$PlaidAccount->mask = $Account->getMask();
					$PlaidAccount->type = $Account->getType();
					$PlaidAccount->subtype = $Account->getSubtype();
					$PlaidAccount->includeInUpdates = true;
					$PlaidAccount->save();
					$PlaidAccounts->push($PlaidAccount);
				}
			}

			$Institution = $Plaid->getInstitutionById($PlaidData->institutionId);

			$AllPlaidAccounts[] = [
				'plaidDataId' => $PlaidData->id,
				'institution' => $Institution,
				'accounts' => $PlaidAccounts,
			];
		}

		return new Response($AllPlaidAccounts);
	}

	/**
	 * updateAccount
	 *
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function updateAccount(int $id, Request $Request): Response {
		$PlaidAccount = PlaidAccount::findOrFail($id);
		$PlaidAccount->includeInUpdates = $Request->get('includeInUpdates');
		$PlaidAccount->save();

		return new Response('Updated');
	}
}