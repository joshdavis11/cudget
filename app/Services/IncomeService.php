<?php

namespace App\Services;

use App\Exceptions\PermissionsException;
use App\Model\Income;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class IncomeService
 *
 * @package App\Http\Services
 */
class IncomeService {
	/**
	 * @var AuthManager
	 */
	protected $Auth;

	/**
	 * IncomeService constructor.
	 *
	 * @param AuthManager $Auth
	 */
	public function __construct(AuthManager $Auth) {
		$this->Auth = $Auth;
	}

	/**
	 * Get income
	 *
	 * @param $id
	 *
	 * @return Income
	 * @throws ModelNotFoundException
	 */
	public function getIncome($id) {
		return Income::findOrFail($id);
	}

	/**
	 * Update Income
	 *
	 * @param         $id
	 * @param Request $request
	 *
	 * @return bool|int
	 * @throws ModelNotFoundException
	 */
	public function updateIncome($id, Request $request) {
		$request->merge(['datetime' => date('Y-m-d 00:00:00', strtotime($request->input('datetime')))]);
		return $this->getIncome($id)->update($request->only(['userId', 'datetime', 'description', 'incomeCategoryId', 'amount']));
	}

	/**
	 * Create Income
	 *
	 * @param Request $request
	 *
	 * @return Income
	 */
	public function createIncome(Request $request) {
		$request->merge(['datetime' => date('Y-m-d 00:00:00', strtotime($request->input('datetime')))]);
		return Income::create($request->only(['userId', 'datetime', 'description', 'incomeCategoryId', 'amount']));
	}

	/**
	 * Get all income for a given user
	 *
	 * @param int $userId
	 *
	 * @return Collection
	 */
	public function getAllIncomeForUser($userId) {
		return Income::with('incomeCategory')
			->with('budgetIncome')
			->where('user_id', '=', $userId)
			->orderBy('datetime', 'DESC')
			->get();
	}

	/**
	 * Delete income
	 *
	 * @param int $id
	 *
	 * @return void
	 * @throws PermissionsException
	 */
	public function deleteIncome($id) {
		$Income = $this->getIncome($id);
		if ($Income->userId !== $this->Auth->user()->id) {
			throw new PermissionsException("You can't delete that income.");
		}
		$Income->delete();
	}
}