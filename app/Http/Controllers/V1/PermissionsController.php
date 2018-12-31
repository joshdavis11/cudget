<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Model\Permission;
use App\Services\PermissionsService;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class PermissionsController
 *
 * @package App\Http\Controllers
 */
class PermissionsController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @param PermissionsService $PermissionsService
	 *
	 * @return Response
	 */
	public function index(PermissionsService $PermissionsService) {
		return new Response($PermissionsService->getAll());
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

	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 *
	 * @return Response
	 */
	public function show(int $id, PermissionsService $PermissionsService) {
		return new Response($PermissionsService->getById($id));
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
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function update(Request $Request) {

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
