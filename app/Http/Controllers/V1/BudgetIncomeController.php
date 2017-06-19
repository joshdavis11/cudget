<?php

namespace App\Http\Controllers\V1;

use App\Services\BudgetIncomeService;
use App\Services\IncomeService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class BudgetIncomeController
 *
 * @package App\Http\Controllers
 */
class BudgetIncomeController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;
	/**
	 * @var BudgetIncomeService
	 */
	protected $BudgetIncomeService;
	/**
	 * @var IncomeService
	 */
	protected $IncomeService;

	public function __construct(AuthManager $Auth, BudgetIncomeService $BudgetIncomeService, IncomeService $IncomeService) {
		$this->middleware('auth');
		$this->Auth = $Auth;
		$this->BudgetIncomeService = $BudgetIncomeService;
		$this->IncomeService = $IncomeService;
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
		$request->merge(['userId' => $this->Auth->user()->id]);

		try {
			$BudgetIncome = $this->BudgetIncomeService->createBudgetIncome($request);
		} catch (Exception $exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response($BudgetIncome);
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
		$incomeId = $request->input('incomeId');
		if (!$incomeId) {
			try {
				$incomeId = $this->BudgetIncomeService->getBudgetIncome($id)->incomeId;
			} catch (Exception $exception) {
				return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
			}
		}

		$request->merge(['userId' => $this->Auth->user()->id]);
		try {
			$this->IncomeService->updateIncome($incomeId, new Request($request->input('income')));
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
			$this->BudgetIncomeService->deleteBudgetIncome($id);
		} catch (Exception $Exception) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}
		return new Response(['message' => 'Income was removed from this budget.'], Response::HTTP_OK, ['Content-Type' => 'application/json']);
	}
}
