<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\PlaidRequestException;
use App\Http\Controllers\Controller;
use App\Model\Permission;
use App\Model\PlaidAccount;
use App\Model\PlaidData;
use App\Services\ImportService;
use App\Services\PermissionsService;
use App\Services\UserService;
use App\Utilities\Plaid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Unirest\Exception;

/**
 * Class BankingController
 *
 * @package app\Http\Controllers\V1
 */
class BankingController extends Controller {
	/**
	 * update
	 *
	 * @param Request            $Request
	 * @param ImportService      $ImportService
	 * @param UserService        $UserService
	 * @param PermissionsService $PermissionsService
	 *
	 * @return Response
	 */
	public function update(Request $Request, ImportService $ImportService, UserService $UserService, PermissionsService $PermissionsService) {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_ACCOUNTS);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

		try {
			$Budget = $ImportService->updateFromPlaid($userId);
		} catch(PlaidRequestException $plaidRequestException) {
			return new Response($plaidRequestException, Response::HTTP_CONFLICT);
		} catch(Exception $exception) {
			return new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		$headers = [];
		if (!empty($Budget->id)) {
			$headers['Location'] = '/budgets/' . $Budget->id;
		}

		return new Response(['budgetId' => $Budget->id ?? null], Response::HTTP_OK, $headers);
	}

	/**
	 * getAccounts
	 *
	 * @param Request            $Request
	 * @param Plaid              $Plaid
	 * @param PermissionsService $PermissionsService
	 * @param UserService        $UserService
	 *
	 * @return Response
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	public function getAccounts(Request $Request, Plaid $Plaid, PermissionsService $PermissionsService, UserService $UserService): Response {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_ACCOUNTS);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

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
				'lastUpdated' => $PlaidData->lastRun,
			];
		}

		return new Response($AllPlaidAccounts);
	}

	/**
	 * updateAccount
	 *
	 * @param int                $id
	 * @param Request            $Request
	 * @param PermissionsService $PermissionsService
	 * @param UserService        $UserService
	 *
	 * @return Response
	 */
	public function updateAccount(int $id, Request $Request, PermissionsService $PermissionsService, UserService $UserService): Response {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_ACCOUNTS);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

		$PlaidAccount = PlaidAccount::findOrFail($id);
		$PlaidAccount->includeInUpdates = $Request->get('includeInUpdates');
		$PlaidAccount->save();

		return new Response('Updated');
	}
}