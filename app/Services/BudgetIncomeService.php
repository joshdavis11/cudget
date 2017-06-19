<?php

namespace App\Services;

use App\Model\BudgetIncome;
use App\Model\Income;
use Illuminate\Http\Request;

/**
 * Class BudgetIncomeService
 *
 * @package App\Http\Services
 */
class BudgetIncomeService {
	/**
	 * @var IncomeService
	 */
	protected $IncomeService;

	/**
	 * BudgetIncomeService constructor.
	 *
	 * @param IncomeService $IncomeService
	 */
	public function __construct(IncomeService $IncomeService) {
		$this->IncomeService = $IncomeService;
	}

	/**
	 * Get a budget income
	 *
	 * @param int $id
	 *
	 * @return BudgetIncome
	 */
	public function getBudgetIncome($id) {
		return BudgetIncome::findOrFail($id);
	}

	/**
	 * Create a new budget income
	 *
	 * @param Request $request
	 *
	 * @return BudgetIncome
	 */
	public function createBudgetIncome(Request $request) {
		$incomeRequest = new Request($request->input('income'));
		$incomeRequest->merge(['userId' => $request->input('userId')]);

		//Save the income
		if ($incomeRequest->input('id')) {
			$Income = $this->IncomeService->getIncome($incomeRequest->input('id'));
		} else {
			$Income = $this->IncomeService->createIncome($incomeRequest);
		}

		//Save the budget income relationship
		$BudgetIncome = new BudgetIncome($request->only(['budgetId']));
		$BudgetIncome->income()->associate($Income)->save();

		return $BudgetIncome;
	}

	/**
	 * Delete a budget income from a budget
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function deleteBudgetIncome($id) {
		BudgetIncome::destroy($id);
	}
}