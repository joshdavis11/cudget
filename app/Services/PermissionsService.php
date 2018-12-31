<?php

namespace App\Services;

use App\Model\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class PermissionsService
 *
 * @package App\Services
 */
class PermissionsService {
	/**
	 * getAll
	 *
	 * @return Permission[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function getAll() {
		return Permission::all();
	}

	/**
	 * getById
	 *
	 * @param int $id
	 *
	 * @return Permission
	 * @throws ModelNotFoundException
	 */
	public function getById(int $id): Permission {
		return Permission::findOrFail($id);
	}

	/**
	 * getByDefinition
	 *
	 * @param string $definition
	 *
	 * @return Permission
	 */
	public function getByDefinition(string $definition): Permission {
		return Permission::where('definition', '=', $definition)->firstOrFail();
	}
}