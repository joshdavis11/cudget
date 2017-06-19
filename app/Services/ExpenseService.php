<?php

namespace App\Services;

use App\Model\Expense;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ExpenseService {
	/**
	 * Get income
	 *
	 * @param $id
	 *
	 * @return Expense
	 * @throws ModelNotFoundException
	 */
	public function getExpense($id) {
		return Expense::findOrFail($id);
	}

	/**
	 * Create Expense
	 *
	 * @param Request $request
	 *
	 * @return Expense
	 */
	public function createExpense(Request $request) {
		$request->merge(['datetime' => date('Y-m-d 00:00:00', strtotime($request->input('datetime')))]);
		return Expense::create($request->only(['userId', 'datetime', 'description', 'expenseCategoryId', 'amount']));
	}

	/**
	 * Update Expense
	 *
	 * @param int     $id
	 * @param Request $request
	 *
	 * @return bool|int
	 */
	public function updateExpense($id, Request $request) {
		$request->merge(['datetime' => date('Y-m-d 00:00:00', strtotime($request->input('datetime')))]);
		return $this->getExpense($id)->update($request->only(['userId', 'datetime', 'description', 'expenseCategoryId', 'amount']));
	}
}