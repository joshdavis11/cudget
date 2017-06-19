<?php

namespace App\Http\Controllers\V1;

use App\Services\BudgetService;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Class BudgetCategoryRowsController
 *
 * @package App\Http\Controllers
 */
class BudgetCategoryRowsController extends Controller {
	/**
	 * @var BudgetService
	 */
	protected $BudgetService;

	/**
	 * BudgetCategoriesController constructor.
	 *
	 * @param BudgetService $BudgetService
	 */
	public function __construct(BudgetService $BudgetService) {
		$this->BudgetService = $BudgetService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Get budget category rows for a given budget category
	 *
	 * @param int $budgetCategoryId
	 *
	 * @return Response
	 */
	public function forBudgetCategory($budgetCategoryId) {
		$BudgetCategoryRows = $this->BudgetService->getBudgetCategoryRowsForBudgetCategory($budgetCategoryId);
		return new Response($BudgetCategoryRows);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		try {
			$BudgetCategoryRow = $this->BudgetService->createBudgetCategoryRow($request);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['BudgetCategoryRow' => $BudgetCategoryRow, 'message' => $BudgetCategoryRow->name . ' added!'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
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
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		try {
			$this->BudgetService->updateBudgetCategoryRow($id, $request);
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
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$this->BudgetService->deleteBudgetCategoryRow($id);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['message' => 'Budget category row removed.'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}
}
