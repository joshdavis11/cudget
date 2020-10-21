<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use App\Model\Expense;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ExpenseController
 *
 * @package App\Http\Controllers
 */
class ExpenseController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;
	/**
	 * @var ExpenseService
	 */
	protected $ExpenseService;

	/**
	 * ExpenseController constructor.
	 *
	 * @param AuthManager    $Auth
	 * @param ExpenseService $ExpenseService
	 */
	public function __construct(AuthManager $Auth, ExpenseService $ExpenseService) {
		$this->Auth = $Auth;
		$this->ExpenseService = $ExpenseService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$Expenses = Expense::with('budgetCategoryRowExpense')
			->where('user_id', '=', $this->Auth->user()->id)
			->orderBy('datetime', 'DESC')
			->get();
		return new Response($Expenses);
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
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function store(Request $Request) {
		$Request->merge(['userId' => $this->Auth->user()->id]);
		$Expense = $this->ExpenseService->createExpense($Request);
		return new Response($Expense, Response::HTTP_CREATED, ['Location' => '/api/v1/expenses/' . $Expense->id]);
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
	 * @param  Request $Request
	 * @param  int     $id
	 *
	 * @return Response
	 */
	public function update(Request $Request, $id) {
		$authUserId = $this->Auth->user()->id;
		$Expense = $this->ExpenseService->getExpense($id);
		if ($authUserId !== $Expense->userId) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		$Request->merge(['userId' => $authUserId]);
		$this->ExpenseService->updateExpense($id, $Request);

		return new Response('Expense updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$Expense = $this->ExpenseService->getExpense($id);
		if ($this->Auth->user()->id !== $Expense->userId) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		$Expense->delete();
		return new Response('Expense deleted');
	}
}
