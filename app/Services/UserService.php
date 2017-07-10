<?php

namespace App\Services;

use App\Exceptions\InvalidDataException;
use App\Exceptions\PermissionsException;
use App\Model\User;
use App\Model\UserPermission;
use Illuminate\Auth\AuthManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * @package App\Http\Services
 */
class UserService {
	/**
	 * @var AuthManager
	 */
	private $Auth;

	/**
	 * UserService constructor.
	 *
	 * @param AuthManager $Auth
	 */
	public function __construct(AuthManager $Auth) {
		$this->Auth = $Auth;
	}

	/**
	 * Create a user
	 *
	 * @param Request $Request
	 *
	 * @return User
	 * @throws InvalidDataException
	 */
	public function createUser(Request $Request) {
		$data = $Request->only(['firstName', 'lastName', 'email', 'repeatEmail', 'username', 'phone', 'password', 'repeatPassword', 'admin']);
		if (User::validate($data)) {
			$errors = User::errors();
			throw new InvalidDataException(implode(', ', $errors->getMessages()));
		}
		$data['password'] = Hash::make($data['password']);

		return User::create($data);
	}

	/**
	 * Get a user
	 *
	 * @param int $id
	 *
	 * @return User
	 * @throws ModelNotFoundException
	 */
	public function getUser($id) {
		return User::findOrFail($id);
	}

	/**
	 * Update a user
	 *
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return bool
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function updateUser($id, Request $Request) {
		$data = $Request->only(['firstName', 'lastName', 'email', 'username', 'phone', 'admin']);
		if (User::validate($data)) {
			$errors = User::errors();
			throw new InvalidDataException(implode(', ', $errors->getMessages()));
		}

		return $this->getUser($id)->update($data);
	}

	/**
	 * Change a password for a user
	 *
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return bool|int
	 * @throws InvalidDataException
	 * @throws ModelNotFoundException
	 */
	public function changePassword($id, Request $Request) {
		$data = $Request->only(['password', 'repeatPassword']);
		if (User::validate($data)) {
			$errors = User::errors();
			throw new InvalidDataException(implode(', ', $errors->getMessages()));
		}

		return $this->getUser($id)->update(['password' => Hash::make($data['password'])]);
	}

	/**
	 * deleteUser
	 *
	 * @param $id
	 *
	 * @return bool|null
	 * @throws PermissionsException
	 */
	public function deleteUser($id) {
		if (!$this->Auth->user()->admin) {
			throw new PermissionsException("You don't have permission to remove that user.");
		}
		return $this->getUser($id)->delete();
	}

	/**
	 * Get permissions for a given user
	 *
	 * @param int $userId
	 *
	 * @return Collection
	 */
	public function getUserPermissions($userId) {
		return UserPermission::where('user_id', '=', $userId)->get();
	}

	/**
	 * Get view permissions for a given user
	 *
	 * @param int $userId
	 *
	 * @return Collection
	 */
	public function getViewUserPermissions($userId) {
		return UserPermission::where('user_id', '=', $userId)->where('permission', '=', 'view')->get();
	}

	/**
	 * Get a user permission by ID
	 *
	 * @param $id
	 *
	 * @return UserPermission
	 * @throws ModelNotFoundException
	 */
	public function getUserPermissionById($id) {
		return UserPermission::findOrFail($id);
	}

	/**
	 * Get a permission by id for a user
	 *
	 * @param $userId
	 * @param $id
	 *
	 * @return UserPermission
	 * @throws ModelNotFoundException
	 */
	public function getUserPermission($userId, $id) {
		$UserPermission = $this->getUserPermissionById($id);
		if ($UserPermission->userId !== $userId) {
			throw new ModelNotFoundException();
		}

		return $UserPermission;
	}

	/**
	 * Create a user permission
	 *
	 * @param Request $Request
	 *
	 * @return UserPermission
	 * @throws InvalidDataException
	 */
	public function createUserPermission(Request $Request) {
		$data = $Request->only(['access', 'permission', 'section', 'userId']);
		if (UserPermission::validate($data)) {
			$errors = UserPermission::errors();
			throw new InvalidDataException(implode(', ', $errors->getMessages()));
		}

		return UserPermission::create($data);
	}

	/**
	 * Update a user permission
	 *
	 * @param int     $id
	 * @param Request $Request
	 *
	 * @return bool|int
	 * @throws InvalidDataException
	 */
	public function updateUserPermission($userId, $id, Request $Request) {
		$data = $Request->only(['access', 'permission', 'section', 'userId']);
		if ($userId !== $data['userId']) {
			throw new InvalidDataException('User IDs don\'t match');
		}
		if (UserPermission::validate($data)) {
			$errors = UserPermission::errors();
			throw new InvalidDataException(implode(', ', $errors->getMessages()));
		}

		return $this->getUserPermission($userId, $id)->update($data);
	}
}