<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Model\ExpenseCategory;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\UrlGenerator;

/**
 * Class ExpenseCategoriesController
 *
 * @package App\Http\Controllers
 */
class ExpenseCategoriesController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;

	/**
	 * BudgetsController constructor.
	 *
	 * @param AuthManager    $Auth
	 */
	public function __construct(AuthManager $Auth) {
		$this->middleware('auth');
		$this->Auth = $Auth;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$ExpenseCategories = ExpenseCategory::where('user_id', '=', $this->Auth->user()->id)->orderBy('name', 'ASC')->get();
		return new Response($ExpenseCategories->toJson(), Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
	 * Store a newly created expense category in storage.
	 *
	 * @param Request      $Request
	 * @param UrlGenerator $UrlGenerator
	 *
	 * @return Response
	 */
	public function store(Request $Request, UrlGenerator $UrlGenerator) {
		$ExpenseCategory = ExpenseCategory::create(['name' => $Request->input('name'), 'userId' => $this->Auth->user()->id]);
		return new Response($ExpenseCategory, Response::HTTP_CREATED, ['Location' => $UrlGenerator->to('/api/v1/income/categories/' . $ExpenseCategory->id)]);
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
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
