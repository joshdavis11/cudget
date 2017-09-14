<?php

namespace App\Services;

use App\Exceptions\PermissionsException;
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
	 * @var BudgetService
	 */
	protected $BudgetService;

	/**
	 * BudgetIncomeService constructor.
	 *
	 * @param IncomeService $IncomeService
	 * @param BudgetService $BudgetService
	 */
	public function __construct(IncomeService $IncomeService, BudgetService $BudgetService) {
		$this->IncomeService = $IncomeService;
		$this->BudgetService = $BudgetService;
	}

	/**
	 * Get a budget income
	 *
	 * @param int $id
	 *
	 * @return BudgetIncome
	 */
	public function getBudgetIncome(int $id) {
		return BudgetIncome::findOrFail($id);
	}

	/**
	 * Create a new budget income
	 *
	 * @param Request $request
	 *
	 * @return BudgetIncome
	 * @throws PermissionsException
	 */
	public function createBudgetIncome(Request $request) {
		$budgetId = $request->get('budgetId');
		$this->BudgetService->checkBudgetPermission($budgetId);

		$incomeRequest = new Request($request->input('income'));
		$incomeRequest->merge(['userId' => $request->input('userId')]);

		//Save the income
		if ($incomeRequest->input('id')) {
			$Income = $this->IncomeService->getIncome($incomeRequest->input('id'));
		} else {
			$Income = $this->IncomeService->createIncome($incomeRequest);
		}

		//Save the budget income relationship
		$BudgetIncome = new BudgetIncome(['budgetId' => $budgetId]);
		$BudgetIncome->income()->associate($Income)->save();

		return $BudgetIncome;
	}

	/**
	 * Update a budget income
	 *
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return bool|int
	 * @throws PermissionsException
	 */
	public function updateBudgetIncome(int $id, Request $request) {
		$BudgetIncome = $this->getBudgetIncome($id);
		$this->BudgetService->checkBudgetPermission($BudgetIncome->budgetId);

		return $this->IncomeService->updateIncome($BudgetIncome->incomeId, new Request($request->input('income')));
	}

	/**
	 * Delete a budget income from a budget
	 *
	 * @param int $id
	 *
	 * @return bool|null
	 */
	public function deleteBudgetIncome(int $id) {
		$BudgetIncome = $this->getBudgetIncome($id);
		$this->BudgetService->checkBudgetPermission($BudgetIncome->budgetId);

		return $BudgetIncome->delete();
	}
}