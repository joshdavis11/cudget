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
 * Class BudgetsController
 *
 * @package App\Http\Controllers
 */
class BudgetsController extends Controller {
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
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$authUserId = $this->Auth->user()->id;
		$Budgets = $this->BudgetService->getBudgetsForUser($authUserId);
		foreach ($Budgets as $Budget) {
			if ($Budget->userId == $authUserId) {
				$Budget->createdByUser = 'Me';
			} else {
				$Budget->createdByUser = $Budget->user->firstName . ' ' . $Budget->user->lastName;
			}
		}
		return new Response($Budgets->toJson(), Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$BudgetTemplates = $this->BudgetService->getBudgetTemplatesForUser($this->Auth->user()->id);
		return new Response(['BudgetTemplates' => $BudgetTemplates], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$request->merge(['userId' => $this->Auth->user()->id, 'template' => false]);
		try {
			$Budget = $this->BudgetService->createBudget($request);
		} catch (InvalidDataException $InvalidDataException) {
			return new Response($InvalidDataException->getMessage(), Response::HTTP_BAD_REQUEST);
		}

		return new Response($Budget, Response::HTTP_CREATED, ['Location' => '/api/v1/budgets/' . $Budget->id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id) {
		try {
			$Budget = $this->BudgetService->getBudgetWithRelations($id);
			$this->BudgetService->saveLastAccess($id);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		return new Response($Budget, Response::HTTP_OK);
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

		$Budget = $this->BudgetService->getBudget($id);
		$BudgetTemplates = $this->BudgetService->getBudgetTemplatesForUser($this->Auth->user()->id);

		return new Response(['Budget' => $Budget, 'BudgetTemplates' => $BudgetTemplates], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int                      $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, int $id) {
		$request->merge(['userId' => $this->Auth->user()->id, 'template' => false]);
		try {
			$this->BudgetService->updateBudget($id, $request);
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
	public function destroy(int $id) {
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
			$Budget = $this->BudgetService->getBudget($id);
		} catch (ModelNotFoundException $exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		$Users = $this->BudgetService->getUsersAvailableToShareBudgetWith($id);
		if ($Users->isEmpty()) {
			return new Response("No available users to share the budget with...", Response::HTTP_PRECONDITION_FAILED);
		}
		return new Response(['Users' => $Users, 'Budget' => $Budget], Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
