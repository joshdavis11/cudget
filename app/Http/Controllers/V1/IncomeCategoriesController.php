<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Model\IncomeCategory;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\UrlGenerator;

/**
 * Class IncomeCategoriesController
 *
 * @package App\Http\Controllers
 */
class IncomeCategoriesController extends Controller {
	/**
	 * @var AuthManager
	 */
	protected $Auth;

	/**
	 * IncomeCategoriesController constructor.
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
		$IncomeCategories = IncomeCategory::where('user_id', '=', $this->Auth->user()->id)->orderBy('name', 'ASC')->get();
		return new Response($IncomeCategories->toJson(), Response::HTTP_OK, ['Content-Type' => 'application/json']);
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
	 * Store a newly created income category in storage.
	 *
	 * @param Request      $Request
	 * @param UrlGenerator $UrlGenerator
	 *
	 * @return Response
	 */
	public function store(Request $Request, UrlGenerator $UrlGenerator) {
		$IncomeCategory = IncomeCategory::create(['name' => $Request->input('name'), 'userId' => $this->Auth->user()->id]);
		return new Response($IncomeCategory, Response::HTTP_CREATED, ['Location' => $UrlGenerator->to('/api/v1/income/categories/' . $IncomeCategory->id)]);
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
