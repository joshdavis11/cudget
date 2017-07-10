<?php

namespace App\Http\Requests\Users;

/**
 * Class SignUpUserRequest
 *
 * @package App\Http\Requests\Users
 */
class SignUpUserRequest extends CreateUserRequest {
	/**
	 * authorize
	 *
	 * @return bool
	 */
	public function authorize(): bool {
		return true;
	}

	/**
	 * rules
	 *
	 * @return array
	 */
	public function rules(): array {
		$rules = parent::rules();
		unset($rules['admin']);
		return $rules;
	}
}