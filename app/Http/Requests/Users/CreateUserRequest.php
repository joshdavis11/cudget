<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseFormRequest;

/**
 * Class StoreUserRequest
 *
 * @package App\Http\Requests\Users
 */
class CreateUserRequest extends BaseFormRequest {
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
			'firstName' => 'required',
			'lastName' => 'required',
			'email' => 'required|email|unique:users,email',
			'repeatEmail' => 'required|email|same:email',
			'username' => 'unique:users,username',
			'phone' => 'numeric',
			'password' => 'required',
			'repeatPassword' => 'required|same:password',
			'admin' => 'required|boolean',
		];
	}
}