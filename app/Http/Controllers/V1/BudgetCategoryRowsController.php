<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\PermissionsException;
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
		try {
			$BudgetCategoryRows = $this->BudgetService->getBudgetCategoryRowsForBudgetCategory($budgetCategoryId);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

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
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(null, Response::HTTP_CREATED, ['Location' => route('budgetCategoryRow.show', $BudgetCategoryRow->id)]);
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
	public function update(Request $request, int $id) {
		try {
			$this->BudgetService->updateBudgetCategoryRow($id, $request);
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
			$this->BudgetService->deleteBudgetCategoryRow($id);
		} catch (PermissionsException $PermissionsException) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		return new Response(null, Response::HTTP_OK);
	}
}
