<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\BudgetService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BudgetCategoriesController
 *
 * @package App\Http\Controllers
 */
class BudgetCategoriesController extends Controller {
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
		$this->Auth = $Auth;
		$this->BudgetService = $BudgetService;
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
	 * Get budget categories for a given budget
	 *
	 * @param int $budgetId
	 *
	 * @return Response
	 */
	public function forBudget($budgetId) {
		$BudgetCategories = $this->BudgetService->getBudgetCategoriesForBudget($budgetId);
		return new Response($BudgetCategories);
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
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return Response
	 */
	public function store(Request $request) {
		try {
			$BudgetCategory = $this->BudgetService->createBudgetCategory($request);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['BudgetCategory' => $BudgetCategory, 'message' => $BudgetCategory->name . ' added!'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
	 * @param  \Illuminate\Http\Request $request
	 * @param  int                      $id
	 *
	 * @return Response
	 */
	public function update(Request $request, $id) {
		try {
			$this->BudgetService->updateBudgetCategory($id, $request);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['message' => $request->input('name') . ' updated!'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
			$this->BudgetService->deleteBudgetCategory($id);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['message' => 'Budget category removed.'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}
}
