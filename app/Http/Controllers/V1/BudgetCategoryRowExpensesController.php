<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\PermissionsException;
use App\Http\Controllers\Controller;
use App\Services\BudgetService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BudgetCategoryRowExpensesController
 *
 * @package App\Http\Controllers
 */
class BudgetCategoryRowExpensesController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;
	/**
	 * @var BudgetService
	 */
	protected $BudgetService;

	/**
	 * BudgetCategoriesController constructor.
	 *
	 * @param AuthManager   $Auth
	 * @param BudgetService $BudgetService
	 */
	public function __construct(AuthManager $Auth, BudgetService $BudgetService) {
		$this->BudgetService = $BudgetService;
		$this->Auth = $Auth;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request $request
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		$request->merge(['userId' => $this->Auth->user()->id]);
		try {
			$BudgetCategoryRowExpense = $this->BudgetService->createBudgetCategoryRowExpense($request);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		return new Response($BudgetCategoryRowExpense, Response::HTTP_CREATED, ['Location' => route('budgetCategoryRowExpense.show', ['expense' => $BudgetCategoryRowExpense->id])]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id) {
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request $request
	 * @param  int     $id
	 *
	 * @return Response
	 */
	public function update(Request $request, int $id) {
		try {
			$this->BudgetService->updateBudgetCategoryRowExpense($id, $request);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		return new Response(null, Response::HTTP_OK);
	}

	/**
	 * Update the specified resources in storage.
	 *
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function bulkUpdate(Request $Request) {
		foreach ($Request->all() as $arrayOfData) {
			try {
				$this->BudgetService->updateBudgetCategoryRowExpense($arrayOfData['id'], new Request($arrayOfData));
			} catch (PermissionsException $PermissionsException) {
				//If we catch a permissions issue, just skip it. We'll do the bulk update for all with perms to do so.
				continue;
			}
		}
		return new Response(null, Response::HTTP_OK);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy($id) {
		try {
			$this->BudgetService->deleteBudgetCategoryRowExpense($id);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		return new Response(null, Response::HTTP_OK);
	}
}
