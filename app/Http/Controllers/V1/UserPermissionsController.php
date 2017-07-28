<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidDataException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\CreateUserPermissionRequest;
use App\Services\UserService;
use Illuminate\Auth\AuthManager;
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
	 * Get perms for the authenticated user
	 *
	 * @param UserService $UserService
	 * @param AuthManager $Auth
	 *
	 * @return Response
	 */
	public function perms(UserService $UserService, AuthManager $Auth) {
		return new Response($UserService->getUserPermissions($Auth->user()->id));
	}

	/**
	 * Get the authenticated user
	 *
	 * @param AuthManager $Auth
	 *
	 * @return Response
	 */
	public function authUser(AuthManager $Auth) {
		return new Response($Auth->user());
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param int $userId
	 *
	 * @return Response
	 */
	public function index(int $userId) {
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
	 * @param CreateUserPermissionRequest $Request
	 *
	 * @return Response
	 */
	public function store(CreateUserPermissionRequest $Request) {
		$UserPermission = $this->UserService->createUserPermission($Request);
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
//		try {
//			$this->UserService->updateUserPermission((int)$userId, (int)$id, $Request);
//		} catch (InvalidDataException $InvalidDataException) {
//			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
//		}
//
//		return new Response('Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $userId
	 * @param int $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(int $userId, int $id) {
		if (!$this->UserService->deleteUserPermission($userId, $id)) {
			return new Response($this->errorProcessingMessage(), Response::HTTP_BAD_REQUEST);
		}

		return new Response('Deleted');
	}
}
