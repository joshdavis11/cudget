<?php

namespace App\Services;

use App\Model\AutoImportAccount;
use App\Model\Budget;
use App\Model\BudgetCategory;
use App\Model\BudgetCategoryRow;
use App\Model\BudgetCategoryRowExpense;
use App\Model\BudgetIncome;
use App\Model\Expense;
use App\Model\Income;
use App\Model\PlaidAccount;
use App\Model\PlaidData;
use App\Utilities\Plaid;
use DateTimeImmutable;
use Dotenv\Exception\InvalidFileException;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;

/**
 * Class ImportService
 *
 * @package App\Http\Services
 */
class ImportService {
	/**
	 * @var int
	 */
	private $authUserId;
	/**
	 * @var Connection
	 */
	private $Database;
	/**
	 * @var Budget|null
	 */
	private $LatestBudget;
	/**
	 * @var Plaid
	 */
	private $Plaid;

	/**
	 * ImportService constructor.
	 *
	 * @param Connection $Database
	 * @param Plaid      $Plaid
	 */
	public function __construct(Connection $Database, Plaid $Plaid) {
		$this->Database = $Database;
		$this->Plaid = $Plaid;
	}

	/**
	 * Import!
	 *
	 * @param Request $Request
	 *
	 * @return Budget|null
	 * @throws \Throwable
	 */
	public function import(Request $Request) {
		$this->authUserId = $Request->user()->id;

		$file = $Request->file('file');
		if (!$file->isValid() || $file->getClientMimeType() !== 'text/csv') {
			throw new InvalidFileException('Invalid File');
		}

		$importType = $Request->input('importType');
		if (intval($importType) > 0) {
			$AutoImportAccount = AutoImportAccount::findOrFail($importType);
		} else {
			$AutoImportAccount = new AutoImportAccount();
			$AutoImportAccount->date = $Request->input('date');
			$AutoImportAccount->amount = $Request->input('amount');
			$AutoImportAccount->description = $Request->input('description');
		}

		$this->Database->transaction(function() use ($file, $AutoImportAccount) {
			$this->doTheImport($file->getPathname(), $AutoImportAccount);
		});

		return $this->LatestBudget;
	}

	/**
	 * updateFromPlaid
	 *
	 * @param int $authUserId
	 *
	 * @return Budget|null
	 * @throws \Unirest\Exception
	 */
	public function updateFromPlaid(int $authUserId) {
		$this->authUserId = $authUserId;

		$PlaidAccessTokens = PlaidData::where('user_id', '=', $authUserId)->with('plaidAccount')->get();
		foreach($PlaidAccessTokens as $PlaidAccessToken) {
			$accountIds = [];
			foreach($PlaidAccessToken->getRelations()['plaidAccount'] ?? [] as $PlaidAccount) {
				/** @var PlaidAccount $PlaidAccount */
				if ($PlaidAccount->includeInUpdates) {
					$accountIds[] = $PlaidAccount->accountId;
				}
			}

			if (empty($accountIds)) {
				continue;
			}

			/** @var PlaidData $PlaidAccessToken */
			$lastRun = new DateTimeImmutable('now');
			$startDate = $PlaidAccessToken->lastRun ? date('Y-m-d', strtotime($PlaidAccessToken->lastRun . ' -1 day')) : date('Y-m-d', strtotime('1 month ago'));
			$Transactions = $this->Plaid->getTransactions($PlaidAccessToken->accessToken, $startDate, $lastRun->format('Y-m-d'), $accountIds);
			foreach($Transactions as $Transaction) {
				$amount = $Transaction->getAmount();
				if ($amount > 0.00) {
					$this->saveIncome(
						$Transaction->getDate(),
						$amount,
						$Transaction->getName(),
						$Transaction->getCurrencyCode(),
						$Transaction->getTransactionId(),
						$Transaction->getAccountId()
					);
				} else {
					$this->saveExpense(
						$Transaction->getDate(),
						$amount,
						$Transaction->getName(),
						$Transaction->getCurrencyCode(),
						$Transaction->getTransactionId(),
						$Transaction->getAccountId()
					);

				}

			}

			$PlaidAccessToken->lastRun = $lastRun;
			$PlaidAccessToken->save();
		}

		return $this->LatestBudget;
	}

	/**
	 * Parse a CSV file and insert new income and expenses
	 *
	 * @param string            $filePath                The path to the CSV
	 * @param AutoImportAccount $AutoImportAccount The auto import account with the columns for date, amount, and description
	 *
	 * @return void
	 * @throws FileNotFoundException
	 */
	private function doTheImport($filePath, AutoImportAccount $AutoImportAccount) {
		$amountColumnNumber = $AutoImportAccount->amount;
		$datetimeColumnNumber = $AutoImportAccount->date;
		$descriptionColumnNumber = $AutoImportAccount->description;

		if (!($handle = fopen($filePath, 'r'))) {
			throw new FileNotFoundException('Couldn\'t find that file');
		}
		while (($row = fgetcsv($handle, null, ",")) !== false) {

			if (!empty($row[$datetimeColumnNumber])) {
				if ($row[$amountColumnNumber] > 0.00) {
					$this->saveIncome(new DateTimeImmutable(date('Y-m-d H:i:s', strtotime($row[$datetimeColumnNumber]))), $row[$amountColumnNumber], $row[$descriptionColumnNumber]);
				} else {
					$this->saveExpense(new DateTimeImmutable(date('Y-m-d H:i:s', strtotime($row[$datetimeColumnNumber]))), $row[$amountColumnNumber], $row[$descriptionColumnNumber]);
				}
			}
		}
	}

	/**
	 * Save Income
	 *
	 * @param DateTimeImmutable $Date
	 * @param                   $amount
	 * @param                   $description
	 *
	 * @return Income
	 */
	private function saveIncome(DateTimeImmutable $Date, float $amount, string $description, string $isoCurrencyCode = 'USD', string $transactionId = null, string $accountId = null) {
		/** @var Income $Income */
		if (null !== $transactionId) {
			$Income = Income::where('transaction_id', '=', $transactionId)->first();
		}
		if (empty($Expense)) {
			$Income = new Income();
		}
		$Income->userId = $this->authUserId;
		$Income->datetime = $Date->format('Y-m-d H:i:s');
		$Income->amount = abs($amount);
		$Income->description = $this->cleanIncomeDescription($description);
		$Income->isoCurrencyCode = $isoCurrencyCode;
		$Income->transactionId = $transactionId;
		$Income->accountId = $accountId;

		$CommonIncome = Income::where('user_id', '=', $this->authUserId)
			->where('description', 'LIKE', '%' . preg_replace('/\s/', '%', $Income->description) . '%')
			->orderBy('datetime', 'DESC')
			->first();
		if (!empty($CommonIncome->incomeCategoryId)) {
			$Income->incomeCategoryId = $CommonIncome->incomeCategoryId;
		}

		$Income->save();

		$this->addIncomeToBudget($Income, $Date);

		return $Income;
	}

	/**
	 * Clean the income description
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public function cleanIncomeDescription($description) {
		$cleanDescription = preg_replace('/\s(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|Nov(?:ember)?|Dec(?:ember)?)\s/i', '', $description);
		$cleanDescription = preg_replace('/[0-9%]/', '', $cleanDescription);
		return trim(preg_replace('/\s\s/', ' ', $cleanDescription));
	}

	/**
	 * Add income to budget
	 *
	 * @param Income            $Income
	 * @param DateTimeImmutable $Date
	 *
	 * @return void
	 */
	private function addIncomeToBudget(Income $Income, DateTimeImmutable $Date) {
		$Budget = $this->getBudget($Date);

		if (empty($Budget->id)) {
			return;
		}
		$BudgetIncome = new BudgetIncome();
		$BudgetIncome->budgetId = $Budget->id;
		$BudgetIncome->incomeId = $Income->id;
		$BudgetIncome->save();
	}

	/**
	 * saveExpense
	 *
	 * @param DateTimeImmutable $Date
	 * @param float             $amount
	 * @param string            $description
	 * @param string            $isoCurrencyCode
	 * @param string|null       $transactionId
	 * @param string|null       $accountId
	 *
	 * @return Expense
	 */
	private function saveExpense(DateTimeImmutable $Date, float $amount, string $description, string $isoCurrencyCode = 'USD', string $transactionId = null, string $accountId = null) {
		/** @var Expense $Expense */
		if (null !== $transactionId) {
			$Expense = Expense::where('transaction_id', '=', $transactionId)->first();
		}
		if (empty($Expense)) {
			$Expense = new Expense();
		}
		$Expense->userId = $this->authUserId;
		$Expense->datetime = $Date->format('Y-m-d H:i:s');
		$Expense->amount = abs($amount);
		$Expense->description = $this->cleanExpenseDescription($description);
		$Expense->isoCurrencyCode = $isoCurrencyCode;
		$Expense->transactionId = $transactionId;
		$Expense->accountId = $accountId;

		$CommonExpense = Expense::where('user_id', '=', $this->authUserId)
			->where('description', 'LIKE', '%' . preg_replace('/\s/', '%', $Expense->description) . '%')
			->orderBy('datetime', 'DESC')
			->first();
		if (!empty($CommonExpense->expenseCategoryId)) {
			$Expense->expenseCategoryId = $CommonExpense->expenseCategoryId;
		} elseif (empty($CommonExpense)) {
			$CommonExpense = new Expense();
		}

		$Expense->save();

		$this->addExpenseToBudget($Expense, $CommonExpense, $Date);

		return $Expense;
	}

	/**
	 * Clean the expense description
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public function cleanExpenseDescription($description) {
		$cleanDescription = preg_replace('/PURCHASE AUTHORIZED ON \d\d\/\d\d/', '', $description);
		$cleanDescription = preg_replace('/\d{6,}.*/', '', $cleanDescription);
		return trim(preg_replace('/[%]/', '', $cleanDescription));
	}

	/**
	 * Add an expense to the Budget
	 *
	 * @param Expense           $Expense
	 * @param Expense           $CommonExpense
	 * @param DateTimeImmutable $Date
	 *
	 * @return void
	 */
	private function addExpenseToBudget(Expense $Expense, Expense $CommonExpense, DateTimeImmutable $Date) {
		$Budget = $this->getBudget($Date);

		if (empty($Budget->id)) {
			return;
		}

		//If there is a common expense and we're able to find a match in the new budget, return since we don't
		//need to add it to the uncategorized category.
		if (!empty($CommonExpense->id) && $this->addExpenseToCommonBudgetCategory($Expense, $CommonExpense, $Budget)) {
			return;
		}
		$this->addExpenseToUncategorizedBudgetCategory($Expense, $Budget);
	}

	/**
	 * Add the expense to a common budget category
	 *
	 * @param Expense $Expense
	 * @param Expense $CommonExpense
	 * @param Budget  $Budget
	 *
	 * @return bool
	 */
	private function addExpenseToCommonBudgetCategory(Expense $Expense, Expense $CommonExpense, Budget $Budget) {
		$BudgetCategoryRowExpense = BudgetCategoryRowExpense::where('expense_id', '=', $CommonExpense->id)->first();
		if (!empty($BudgetCategoryRowExpense->id)) {
			$BudgetCategoryRow = BudgetCategoryRow::find($BudgetCategoryRowExpense->budgetCategoryRowId);
			$BudgetCategory = BudgetCategory::find($BudgetCategoryRow->budgetCategoryId);

			$CurrentBudgetsBudgetCategory = BudgetCategory::where('name', '=', $BudgetCategory->name)
				->where('budget_id', '=', $Budget->id)
				->first();

			if (!empty($CurrentBudgetsBudgetCategory->id)) {
				$CurrentBudgetsBudgetCategoryRow = BudgetCategoryRow::where('name', '=', $BudgetCategoryRow->name)
					->where('budget_category_id', '=', $CurrentBudgetsBudgetCategory->id)
					->first();

				if (!empty($CurrentBudgetsBudgetCategoryRow->id)) {
					$NewBudgetCategoryRowExpense = new BudgetCategoryRowExpense();
					$NewBudgetCategoryRowExpense->budgetCategoryRowId = $CurrentBudgetsBudgetCategoryRow->id;
					$NewBudgetCategoryRowExpense->expenseId = $Expense->id;
					try {
						$NewBudgetCategoryRowExpense->save();
					} catch (Exception $Exception) {
						return false;
					}

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Add an expense to the uncategorized budget category
	 *
	 * @param Expense $Expense
	 * @param Budget  $Budget
	 *
	 * @return bool
	 */
	private function addExpenseToUncategorizedBudgetCategory(Expense $Expense, Budget $Budget) {
		$BudgetCategory = BudgetCategory::where("budget_id", "=", $Budget->id)
			->where('name', '=', 'Uncategorized')
			->first();
		if (!empty($BudgetCategory->id)) {
			$BudgetCategoryRow = BudgetCategoryRow::where("budget_category_id", "=", $BudgetCategory->id)
				->where('name', '=', 'Uncategorized')
				->first();
			if (!empty($BudgetCategoryRow->id)) {
				$BudgetCategoryRowExpense = new BudgetCategoryRowExpense();
				$BudgetCategoryRowExpense->budgetCategoryRowId = $BudgetCategoryRow->id;
				$BudgetCategoryRowExpense->expenseId = $Expense->id;
				try {
					$BudgetCategoryRowExpense->save();
				} catch (Exception $Exception) {
					return false;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Get a budget where the given date is between the start and end date of the budget
	 *
	 * @param DateTimeImmutable $Date
	 *
	 * @return Budget
	 */
	private function getBudget(DateTimeImmutable $Date) {
		$Budget = Budget::whereRaw('"' . $Date->format('Y-m-d H:i:s') . '" BETWEEN start AND end')
						->where('user_id', '=', $this->authUserId)
						->orderBy('start', 'DESC')
						->first();
		if (!empty($Budget->id)) {
			if (empty($this->LatestBudget) || $Budget->start > $this->LatestBudget->start) {
				$this->LatestBudget = $Budget;
			}
		}
		return $Budget;
	}
}