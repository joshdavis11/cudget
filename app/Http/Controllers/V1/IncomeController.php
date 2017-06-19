<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\PermissionsException;
use App\Http\Controllers\Controller;
use App\Services\IncomeService;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class IncomeController
 *
 * @package App\Http\Controllers
 */
class IncomeController extends Controller {
	/**
	 * @var IncomeService
	 */
	protected $IncomeService;
	/**
	 * @var AuthManager
	 */
	protected $Auth;

	public function __construct(AuthManager $Auth, IncomeService $IncomeService) {
		$this->middleware('auth');
		$this->Auth = $Auth;
		$this->IncomeService = $IncomeService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$Income = $this->IncomeService->getAllIncomeForUser($this->Auth->user()->id);
		return new Response($Income);
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
	 * @param  Request $Request
	 *
	 * @return Response
	 */
	public function store(Request $Request) {
		$Request->merge(['userId' => $this->Auth->user()->id]);
		$Income = $this->IncomeService->createIncome($Request);
		return new Response($Income, Response::HTTP_CREATED, ['Location' => '/api/v1/income/' . $Income->id]);
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
		$request->merge(['userId' => $this->Auth->user()->id]);
		try {
			$this->IncomeService->updateIncome($id, $request);
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
			$this->IncomeService->deleteIncome($id);
		} catch (PermissionsException $exception) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}

		return new Response('Deleted');
	}
}
