<?php

namespace App\Services;

use App\Exceptions\InvalidDataException;
use App\Http\DataObjects\BudgetData;
use App\Model\Budget;
use App\Model\BudgetCategory;
use App\Model\BudgetCategoryRow;
use App\Model\BudgetCategoryRowExpense;
use App\Model\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/**
 * Class BudgetService
 *
 * @package App\Http\Services
 */
class BudgetService {
	/**
	 * @var AuthManager
	 */
	protected $Auth;
	/**
	 * @var Connection
	 */
	protected $Database;
	/**
	 * @var ExpenseService
	 */
	protected $ExpenseService;

	public function __construct(AuthManager $Auth, Connection $Database, ExpenseService $ExpenseService) {
		$this->Auth = $Auth;
		$this->Database = $Database;
		$this->ExpenseService = $ExpenseService;
	}

	/**
	 * Get a budget
	 *
	 * @param int $id The ID to load
	 *
	 * @return Budget
	 * @throws ModelNotFoundException
	 */
	public function getBudget($id) {
		return Budget::findOrFail($id);
	}

	/**
	 * Get a budget with relations
	 *
	 * @param int $id The ID to load
	 *
	 * @return Budget
	 * @throws ModelNotFoundException
	 */
	public function getBudgetWithRelations($id) {
		return Budget
			::with('budgetIncome')
			->with('budgetIncome.income')
			->with('budgetIncome.income.incomeCategory')
			->with('budgetCategories')
			->with('budgetCategories.budgetCategoryRows')
			->with('budgetCategories.budgetCategoryRows.budgetCategoryRowExpenses')
			->with('budgetCategories.budgetCategoryRows.budgetCategoryRowExpenses.expense')
			->with('budgetCategories.budgetCategoryRows.budgetCategoryRowExpenses.expense.expenseCategory')
			->findOrFail($id);
	}

	/**
	 * Get all budgets for a given user
	 *
	 * @param int $userId The user's ID
	 *
	 * @return Budget[]
	 */
	public function getBudgetsForUser($userId) {
		return Budget::with('user')
			->where(function($query) use ($userId) {
				$query->whereHas('sharedBudget', function($query) use($userId) {
					$query->where('user_id', '=', $userId);
				})
				->orWhere('user_id', '=', $userId);
			})
			->where('template', '=', 0)
			->orderBy('last_access', 'DESC')
			->get();
	}

	/**
	 * Get all budget templates for a given user
	 *
	 * @param int $userId The user's ID
	 *
	 * @return Budget[]
	 */
	public function getBudgetTemplatesForUser($userId) {
		return Budget::with('user')
			->where(function($query) use ($userId) {
				$query->whereHas('sharedBudget', function($query) use($userId) {
					$query->where('user_id', '=', $userId);
				})
				->orWhere('user_id', '=', $userId);
			})
			->where('template', '=', 1)
			->orderBy('last_access', 'DESC')
			->get();
	}

	/**
	 * Create a budget
	 *
	 * @param Request $request
	 *
	 * @return Budget
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function createBudget(Request $request) {
		if ($request->input('newOrExisting') === 'existing') {
			return $this->createFromExistingBudget($request);
		} elseif ($request->input('newOrExisting') === 'new') {
			$this->validateBudget($request);
			$request->merge(['created' => date('Y-m-d H:i:s')]);
			$this->updateData($request);

			return Budget::create($request->only(['userId', 'name', 'income', 'start', 'end', 'created', 'lastAccess', 'template']));
		}
		throw new InvalidDataException('New or existing must be chosen.');
	}

	/**
	 * Create a budget template
	 *
	 * @param Request $request
	 *
	 * @return Budget
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function createBudgetTemplate(Request $request) {
		if ($request->input('newOrExisting') === 'existing') {
			return $this->createFromExistingBudget($request);
		} elseif ($request->input('newOrExisting') === 'new') {
			$this->validateBudget($request);
			$request->merge(['created' => date('Y-m-d H:i:s')]);
			$this->updateData($request);

			return Budget::create($request->only(['userId', 'name', 'income', 'created', 'lastAccess', 'template']));
		}
		throw new InvalidDataException('New or existing must be chosen.');
	}

	/**
	 * Create a budget from an existing budget (usually a template)
	 *
	 * @param Request $request
	 *
	 * @return Budget
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function createFromExistingBudget(Request $request) {
		if (!$request->has('userId', 'name')) {
			throw new InvalidDataException('You\'re missing something required...');
		}
		$NewBudget = new Budget();
		$this->Database->transaction(function() use ($request) {
			$this->updateData($request);
			$NewBudget = $this->getBudgetWithRelations($request->input('budgetId'))->replicate();
			$NewBudget->userId = $request->input('userId');
			$NewBudget->name = $request->input('name');
			$NewBudget->template = $request->input('template');
			$NewBudget->created = $NewBudget->lastAccess = date('Y-m-d H:i:s');
			$NewBudget->start = $request->input('start');
			$NewBudget->end = $request->input('end');
			$NewBudget->save();

			foreach ($NewBudget->getRelations()['budgetCategories'] as $BudgetCategory) {
				$NewBudgetCategory = $BudgetCategory->replicate();
				$NewBudgetCategory->budgetId = $NewBudget->id;
				$NewBudgetCategory->save();

				foreach ($BudgetCategory->getRelations()['budgetCategoryRows'] as $BudgetCategoryRow) {
					$NewBudgetCategoryRow = $BudgetCategoryRow->replicate();
					$NewBudgetCategoryRow->budgetCategoryId = $NewBudgetCategory->id;
					$NewBudgetCategoryRow->save();
				}
			}
		});
		return $NewBudget;
	}

	/**
	 * Validate an account
	 *
	 * @param Request $request
	 *
	 * @throws InvalidDataException
	 */
	private function validateBudget(Request $request) {
		if (!$request->has('userId', 'name', 'income')) {
			throw new InvalidDataException('You\'re missing something required...');
		}
	}

	/**
	 * Update a budget
	 *
	 * @param int     $id      The ID of the account
	 * @param Request $request The request object
	 *
	 * @return bool
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function updateBudget($id, Request $request) {
		$this->validateBudget($request);
		$this->updateData($request);
		return $this->getBudget($id)->update($request->only(['userId', 'name', 'income', 'start', 'end', 'created', 'lastAccess', 'template']));
	}

	/**
	 * Update a budget template
	 *
	 * @param int     $id      The ID of the account
	 * @param Request $request The request object
	 *
	 * @return bool
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function updateBudgetTemplate($id, Request $request) {
		$this->validateBudget($request);
		$request->merge(['lastAccess' => date('Y-m-d H:i:s')]);
		return $this->getBudget($id)->update($request->only(['userId', 'name', 'income', 'lastAccess', 'template']));
	}

	private function updateData(Request $request) {
		if (!empty($request->input('start'))) {
			$request->merge(['start' => date('Y-m-d 00:00:00', strtotime($request->input('start')))]);
		}
		if (!empty($request->input('end'))) {
			$request->merge(['end' => date('Y-m-d 23:59:59', strtotime($request->input('end')))]);
		}
		$request->merge(['lastAccess' => date('Y-m-d H:i:s')]);
	}

	/**
	 * Delete a budget
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function deleteBudget($id) {
		Budget::destroy($id);
	}

	/**
	 * Get actual income from a Budget
	 *
	 * @param Budget $Budget The budget
	 *
	 * @return float
	 */
	public function getActualIncomeFromBudget(Budget $Budget) {
		$actualIncome = 0.00;
		foreach ($Budget->getRelation('budgetIncome') as $BudgetIncome) {
			$actualIncome += $BudgetIncome->getRelation('income')->amount;
		}
		return $actualIncome;
	}

	/**
	 * Get the total spent from a Budget
	 *
	 * @param Budget $Budget The budget
	 *
	 * @return float
	 */
	public function getTotalSpentFromBudget(Budget $Budget) {
		$totalSpent = 0.00;
		foreach ($Budget->getRelation('budgetCategories') as $BudgetCategory) {
			foreach ($BudgetCategory->getRelation('budgetCategoryRows') as $BudgetCategoryRow) {
				foreach ($BudgetCategoryRow->getRelation('budgetCategoryRowExpenses') as $BudgetCategoryRowExpense) {
					$totalSpent += $BudgetCategoryRowExpense->getRelation('expense')->amount;
				}
			}
		}
		return $totalSpent;
	}

	/**
	 * Get the total estimated from a Budget
	 *
	 * @param Budget $Budget The budget
	 *
	 * @return float
	 */
	public function getTotalEstimatedFromBudget(Budget $Budget) {
		$totalEstimated = 0.00;
		foreach ($Budget->getRelation('budgetCategories') as $BudgetCategory) {
			foreach ($BudgetCategory->getRelation('budgetCategoryRows') as $BudgetCategoryRow) {
				$totalEstimated += $BudgetCategoryRow->estimated;
			}
		}
		return $totalEstimated;
	}
	
	public function getAndSetBudgetDataForBudget(Budget $Budget) {
		$BudgetData = new BudgetData();
		foreach ($Budget->getRelation('budgetCategories') as $BudgetCategory) {
			$BudgetCategory->spent = 0.00;
			$BudgetCategory->estimated = 0.00;

			foreach ($BudgetCategory->getRelation('budgetCategoryRows') as $BudgetCategoryRow) {
				$BudgetCategoryRow->spent = 0.00;
				$BudgetData->totalEstimated += $BudgetCategoryRow->estimated;
				$BudgetCategory->estimated += $BudgetCategoryRow->estimated;

				foreach ($BudgetCategoryRow->getRelation('budgetCategoryRowExpenses') as $BudgetCategoryRowExpense) {
					$amount = $BudgetCategoryRowExpense->getRelation('expense')->amount;
					$BudgetData->totalSpent += $amount;
					$BudgetCategory->spent += $amount;
					$BudgetCategoryRow->spent += $amount;
				}
			}

			$BudgetCategory->remaining = $BudgetCategory->estimated - $BudgetCategory->spent;
		}

		foreach ($Budget->getRelation('budgetIncome') as $BudgetIncome) {
			$BudgetData->actualIncome += $BudgetIncome->getRelation('income')->amount;
		}

		return $BudgetData;
	}

	/**
	 * Save the last access for a budget
	 *
	 * @param int $id The budget's ID
	 *
	 * @return void
	 * @throws ModelNotFoundException
	 */
	public function saveLastAccess($id) {
		$Budget = $this->getBudget($id);
		$Budget->lastAccess = date('Y-m-d H:i:s');
		$Budget->update();
	}

	/**
	 * getUsersAvailableToShareBudgetWith
	 *
	 * @param $budgetId
	 *
	 * @return Collection
	 */
	public function getUsersAvailableToShareBudgetWith($budgetId) {
		return User::whereNotIn('id', function(Builder $query) use ($budgetId) {
			 $query->select('user_id')->from('shared_budgets')->where('budget_id', '=', $budgetId)->get();
		})
		->where('id', '<>', $this->Auth->user()->id)
		->get();
	}

	/**
	 * Get budget categories for a given budget
	 *
	 * @param $budgetId
	 *
	 * @return Collection
	 */
	public function getBudgetCategoriesForBudget($budgetId) {
		return BudgetCategory::where('budget_id', '=', $budgetId)->get();
	}

	/**
	 * Get a BudgetCategory
	 *
	 * @param $id
	 *
	 * @return BudgetCategory
	 * @throws ModelNotFoundException
	 */
	public function getBudgetCategory($id) {
		return BudgetCategory::findOrFail($id);
	}

	/**
	 * Create a BudgetCategory
	 *
	 * @param Request $request
	 *
	 * @return BudgetCategory
	 */
	public function createBudgetCategory(Request $request) {
		$BudgetCategory = new BudgetCategory($request->only(['budgetId', 'name']));
		$BudgetCategory->sortOrder = $BudgetCategory->getMaxSortOrder() + 1;
		$BudgetCategory->save();
		return $BudgetCategory;
	}

	/**
	 * Delete a BudgetCategory
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function deleteBudgetCategory($id) {
		BudgetCategory::destroy($id);
	}

	/**
	 * Update a BudgetCategory
	 *
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return void
	 */
	public function updateBudgetCategory($id, Request $request) {
		$this->getBudgetCategory($id)->update($request->only(['budgetId', 'name', 'sortOrder']));
	}

	/**
	 * Get budget category rows for a given budget category
	 *
	 * @param $budgetCategoryId
	 *
	 * @return mixed
	 */
	public function getBudgetCategoryRowsForBudgetCategory($budgetCategoryId) {
		return BudgetCategoryRow::where('budget_category_id', '=', $budgetCategoryId)->get();
	}

	/**
	 * Get a BudgetCategoryRow
	 *
	 * @param int $id
	 *
	 * @return BudgetCategoryRow
	 * @throws ModelNotFoundException
	 */
	public function getBudgetCategoryRow($id) {
		return BudgetCategoryRow::findOrFail($id);
	}

	/**
	 * Update a BudgetCategoryRow
	 *
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return void
	 */
	public function updateBudgetCategoryRow($id, Request $request) {
		$this->getBudgetCategoryRow($id)->update($request->only(['budgetCategoryId', 'name', 'estimated', 'sortOrder']));
	}

	/**
	 * Create a BudgetCategoryRow
	 *
	 * @param Request $request
	 *
	 * @return BudgetCategoryRow
	 */
	public function createBudgetCategoryRow(Request $request) {
		$BudgetCategoryRow = new BudgetCategoryRow($request->only(['budgetCategoryId', 'name', 'estimated']));
		$BudgetCategoryRow->sortOrder = $BudgetCategoryRow->getMaxSortOrder() + 1;
		$BudgetCategoryRow->save();
		return $BudgetCategoryRow;
	}

	/**
	 * Delete a BudgetCategoryRow
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function deleteBudgetCategoryRow($id) {
		BudgetCategoryRow::destroy($id);
	}

	/**
	 * Get a BudgetCategoryRowExpense
	 *
	 * @param int $id
	 *
	 * @return BudgetCategoryRowExpense
	 */
	public function getBudgetCategoryRowExpense($id) {
		return BudgetCategoryRowExpense::findOrFail($id);
	}

	/**
	 * Create a BudgetCategoryRowExpense
	 *
	 * @param Request $request
	 *
	 * @return BudgetCategoryRowExpense
	 */
	public function createBudgetCategoryRowExpense(Request $request) {
		$expenseRequest = new Request($request->input('expense'));
		$expenseRequest->merge(['userId' => $request->input('userId')]);

		if ($expenseRequest->input('id')) {
			$Expense = $this->ExpenseService->getExpense($expenseRequest->input('id'));
		} else {
			$Expense = $this->ExpenseService->createExpense($expenseRequest);
		}

		$BudgetCategoryRowExpense = new BudgetCategoryRowExpense($request->only(['budgetCategoryRowId']));
		$BudgetCategoryRowExpense->expense()->associate($Expense)->save();
		return $BudgetCategoryRowExpense;
	}

	/**
	 * Update a BudgetCategoryRowExpense
	 *
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return void
	 */
	public function updateBudgetCategoryRowExpense($id, Request $request) {
		$expenseRequest = new Request($request->input('expense'));
		$expenseId = $expenseRequest->input('id');

		$BudgetCategoryRowExpense = $this->getBudgetCategoryRowExpense($id);
		$BudgetCategoryRowExpense->update($request->only(['budgetCategoryRowId']));
		if (!$expenseId) {
			$expenseId = $BudgetCategoryRowExpense->expenseId;
		}
		$expenseRequest->merge(['userId' => $this->Auth->user()->id]);
		$this->ExpenseService->updateExpense($expenseId, $expenseRequest);
	}

	/**
	 * Delete a BudgetCategoryRowExpense
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	public function deleteBudgetCategoryRowExpense($id) {
		BudgetCategoryRowExpense::destroy($id);
	}
}