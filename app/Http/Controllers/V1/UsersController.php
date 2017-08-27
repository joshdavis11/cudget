<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidDataException;
use App\Exceptions\PermissionsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Services\UserService;
use App\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class UsersController
 *
 * @package App\Http\Controllers
 */
class UsersController extends Controller {
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
	public function index() {
		return new Response(User::all());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param CreateUserRequest $Request
	 *
	 * @return Response
	 */
	public function store(CreateUserRequest $Request) {
		$Request->merge(['emailVerified' => true]);
		$User = $this->UserService->createUser($Request);
		return new Response('Created', Response::HTTP_CREATED, ['Location' => '/api/v1/users/' . $User->id]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function show($id) {
		try {
			$User = $this->UserService->getUser($id);
		} catch (ModelNotFoundException $Exception) {
			return new Response('User not found', Response::HTTP_BAD_REQUEST);
		}
		return new Response($User);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit($id) {
		try {
			$User = $this->UserService->getUser($id);
		} catch (ModelNotFoundException $Exception) {
			return new Response('User not found', Response::HTTP_BAD_REQUEST);
		}
		return new Response($User->makeHidden(['name']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $Request
	 * @param int     $id
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $Request, $id) {
		try {
			$this->UserService->updateUser($id, $Request);
		} catch (ModelNotFoundException $Exception) {
			return new Response('User not found', Response::HTTP_BAD_REQUEST);
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
		try {
			$this->UserService->deleteUser($id);
		} catch (ModelNotFoundException $Exception) {
			return new Response('User not found', Response::HTTP_BAD_REQUEST);
		} catch (PermissionsException $Exception) {
			return new Response($this->authorizationMessage(), Response::HTTP_FORBIDDEN);
		}
		return new Response('Deleted');
	}

	/**
	 * Change a password for a user
	 *
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return Response
	 */
	public function password($id, Request $Request) {
		try {
			$this->UserService->changePassword($id, $Request);
		} catch (ModelNotFoundException $Exception) {
			return new Response('User not found', Response::HTTP_BAD_REQUEST);
		}
		return new Response('Password Updated');
	}

	/**
	 * See if an email already exists
	 *
	 * @param string $email
	 *
	 * @return Response
	 */
	public function emailExists(string $email) {
		return new Response(['exists' => $this->UserService->checkEmailExists($email)]);
	}

	/**
	 * See if a username already exists
	 *
	 * @param string $username
	 *
	 * @return Response
	 */
	public function usernameExists(string $username) {
		return new Response(['exists' => $this->UserService->checkUsernameExists($username)]);
	}
}
