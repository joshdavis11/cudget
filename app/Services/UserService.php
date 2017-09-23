<?php

namespace App\Services;

use App\Exceptions\InvalidDataException;
use App\Exceptions\PermissionsException;
use App\Http\Requests\Permissions\CreateUserPermissionRequest;
use App\Model\Permission;
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
		$data = $Request->only(['firstName', 'lastName', 'email', 'repeatEmail', 'username', 'phone', 'password', 'repeatPassword', 'admin', 'emailVerified']);
		$data['password'] = Hash::make($data['password']);
		$User = User::create($data);
		$this->createNewUserPermissions($User->id);

		return $User;
	}

	/**
	 * Create new user permissions. Currently they get access to Import, Budget Templates, and Color Scheme.
	 *
	 * @param int $userId
	 *
	 * @return void
	 */
	private function createNewUserPermissions(int $userId) {
		$Permissions = Permission
			::where('definition', '=', 'import')
			->orWhere('definition', '=', 'budgetTemplates')
			->orWhere('definition', '=', 'colorScheme')
			->get();

		foreach ($Permissions as $Permission) {
			$UserPermission = new UserPermission();
			$UserPermission->userId = $userId;
			$UserPermission->permissionId = $Permission->id;
			$UserPermission->save();
		}
	}

	/**
	 * Get a user
	 *
	 * @param int $id
	 *
	 * @return User
	 * @throws ModelNotFoundException
	 */
	public function getUser(int $id) {
		return User::findOrFail($id);
	}

	/**
	 * Get a user by email
	 *
	 * @param string $email
	 *
	 * @return User
	 * @throws ModelNotFoundException
	 */
	public function getUserByEmail(string $email) {
		return User::where('email', '=', $email)->firstOrFail();
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
	 * Check if the given email already exists
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function checkEmailExists(string $email) {
		return User::where('email', '=', $email)->exists();
	}

	/**
	 * Check if the given username already exists
	 *
	 * @param string $username
	 *
	 * @return bool
	 */
	public function checkUsernameExists(string $username) {
		return User::where('username', '=', $username)->exists();
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
		return UserPermission::with('permission')->where('user_id', '=', $userId)->get();
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
	 * @param CreateUserPermissionRequest $Request
	 *
	 * @return UserPermission
	 * @throws InvalidDataException
	 */
	public function createUserPermission(CreateUserPermissionRequest $Request) {
		$data = $Request->only(['permissionId', 'userId']);
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

	/**
	 * Delete a User Permission
	 *
	 * @param int $userId
	 * @param int $id
	 *
	 * @return bool|null
	 */
	public function deleteUserPermission(int $userId, int $id) {
		return UserPermission::where('user_id', '=', $userId)->where('id', '=', $id)->delete();
	}
}