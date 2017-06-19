<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidDataException;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class UserPermissionsController
 *
 * @package App\Http\Controllers
 */
class UserPermissionsController extends Controller {
	/**
	 * @var UserService
	 */
	private $UserService;

	public function __construct(UserService $UserService) {
		$this->UserService = $UserService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId) {
		return new Response($this->UserService->getUserPermissions($userId));
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
		try {
			$UserPermission = $this->UserService->createUserPermission($Request);
		} catch (InvalidDataException $InvalidDataException) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}

		return new Response($UserPermission, Response::HTTP_CREATED, ['Location' => '/api/users/' . $UserPermission->userId . '/permissions/' . $UserPermission->id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $userId
	 * @param int $id
	 *
	 * @return Response
	 */
	public function show($userId, $id) {
		return new Response($this->UserService->getUserPermission((int)$userId, (int)$id));
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
	 * @param int     $userId
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function update($userId, $id, Request $Request) {
		try {
			$this->UserService->updateUserPermission((int)$userId, (int)$id, $Request);
		} catch (InvalidDataException $InvalidDataException) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}

		return new Response('Updated');
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
