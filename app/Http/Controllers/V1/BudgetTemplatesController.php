<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidDataException;
use App\Exceptions\PermissionsException;
use App\Http\Controllers\Controller;
use App\Services\BudgetService;
use App\Model\SharedBudget;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Illuminate\Http\Response;

/**
 * Class BudgetTemplatesController
 *
 * @package App\Http\Controllers
 */
class BudgetTemplatesController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;

	/**
	 * @var BudgetService
	 */
	protected $BudgetService;

	/**
	 * BudgetsController constructor.
	 *
	 * @param AuthManager   $Auth
	 * @param BudgetService $BudgetService
	 */
	public function __construct(AuthManager $Auth, BudgetService $BudgetService) {
		$this->Auth = $Auth;
		$this->BudgetService = $BudgetService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$authUserId = $this->Auth->user()->id;
		$BudgetTemplates = $this->BudgetService->getBudgetTemplatesForUser($authUserId);
		foreach ($BudgetTemplates as $BudgetTemplate) {
			if ($BudgetTemplate->userId == $authUserId) {
				$BudgetTemplate->createdByUser = 'Me';
			} else {
				$BudgetTemplate->createdByUser = $BudgetTemplate->user->firstName . ' ' . $BudgetTemplate->user->lastName;
			}
		}
		return new Response($BudgetTemplates->toJson(), Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$Budgets = $this->BudgetService->getBudgetsForUser($this->Auth->user()->id);
		return new Response(['Budgets' => $Budgets], Response::HTTP_OK);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$request->merge(['userId' => $this->Auth->user()->id, 'template' => true]);
		try {
			$BudgetTemplate = $this->BudgetService->createBudgetTemplate($request);
		} catch (InvalidDataException $InvalidDataException) {
			return new Response($InvalidDataException->getMessage(), Response::HTTP_BAD_REQUEST);
		}

		return new Response($BudgetTemplate, Response::HTTP_CREATED, ['Location' => '/api/v1/budgets/templates/' . $BudgetTemplate->id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		try {
			$BudgetTemplate = $this->BudgetService->getBudgetWithRelations($id);
			$this->BudgetService->saveLastAccess($id);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		return new Response($BudgetTemplate, Response::HTTP_OK);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function edit(int $id) {
		if (!$this->BudgetService->canSeeBudget($id)) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		$BudgetTemplate = $this->BudgetService->getBudget($id);
		$Budgets = $this->BudgetService->getBudgetsForUser($this->Auth->user()->id);

		return new Response(['BudgetTemplate' => $BudgetTemplate, 'Budgets' => $Budgets], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int                      $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$request->merge(['userId' => $this->Auth->user()->id, 'template' => true]);
		try {
			$this->BudgetService->updateBudgetTemplate($id, $request);
		} catch (InvalidDataException $InvalidDataException) {
			return new Response($InvalidDataException->getMessage(), Response::HTTP_BAD_REQUEST);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		return new Response(null, Response::HTTP_OK);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$this->BudgetService->deleteBudget($id);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		return new Response(null, Response::HTTP_OK);
	}

	/**
	 * Get the share form data
	 *
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getShare($id) {
		try {
			$BudgetTemplate = $this->BudgetService->getBudget($id);
		} catch (ModelNotFoundException $exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		$Users = $this->BudgetService->getUsersAvailableToShareBudgetWith($id);
		if ($Users->isEmpty()) {
			return new Response("No available users to share the template with...", Response::HTTP_PRECONDITION_FAILED);
		}
		return new Response(['Users' => $Users, 'BudgetTemplate' => $BudgetTemplate], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Share a budget
	 *
	 * @param Request $request
	 * @param int     $id
	 *
	 * @return Response
	 */
	public function postShare(Request $request, $id) {
		SharedBudget::create(['userId' => $request->input('userId'), 'budgetId' => $id]);
		return new Response('Budget Shared Successfully!', Response::HTTP_OK);
	}
}
