<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Model\Permission;
use App\Services\ImportService;
use App\Services\PermissionsService;
use App\Services\UserService;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ImportController
 *
 * @package App\Http\Controllers
 */
class ImportController extends Controller {
	/**
	 * import
	 *
	 * @param ImportService      $ImportService
	 * @param Request            $Request
	 * @param PermissionsService $PermissionsService
	 * @param UserService        $UserService
	 *
	 * @return Response
	 * @throws \Throwable
	 */
	public function import(ImportService $ImportService, Request $Request, PermissionsService $PermissionsService, UserService $UserService) {
		$userId = $Request->user()->id;

		$Permission = $PermissionsService->getByDefinition(Permission::DEFINITION_IMPORT);
		if (!$UserService->hasPermission($userId, $Permission->id)) {
			return $this->getInvalidPermissionsResponse();
		}

		try {
			$Budget = $ImportService->import($Request);
		} catch (InvalidFileException $exception) {
			return new Response('Invalid file', Response::HTTP_BAD_REQUEST);
		}
		$headers = [];
		if (!empty($Budget->id)) {
			$headers['Location'] = '/budgets/' . $Budget->id;
		}
		return new Response('Imported!', Response::HTTP_OK, $headers);
	}

	/**
	 * redirectToImport
	 *
	 * @return RedirectResponse
	 */
	public function redirectToImport(): RedirectResponse {
		return redirect()->route('import');
	}
}