<?php

namespace App\Http\Requests\Permissions;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateUserPermissionRequest
 *
 * @package app\Http\Requests\Permissions
 */
class CreateUserPermissionRequest extends BaseFormRequest {
	/**
	 * authorize
	 *
	 * @return bool
	 */
	public function authorize(): bool {
		return $this->user()->admin;
	}

	/**
	 * rules
	 *
	 * @return array
	 */
	public function rules(): array {
		return [
			'userId' => 'required|integer',
			'permissionId' => 'required|integer',
		];
	}
}