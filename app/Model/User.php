<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @package App\Model
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {
	use Authenticatable, Authorizable, CanResetPassword, HasApiTokens;

	/**
	 * @var array
	 */
	static protected $rules = [
		'first_name' => 'required',
		'last_name' => 'required',
		'email' => 'required|email',
		'repeat_email' => 'required|email|same:email',
		'username' => 'required',
		'phone' => 'numeric',
		'password' => 'required',
		'repeat_password' => 'required|same:password',
		'pin' => 'required',
		'repeat_pin' => 'required|same:pin',
		'admin' => 'required|boolean',
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'id' => 'int',
		'admin' => 'bool',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password', 'firstName', 'lastName', 'phone', 'pin', 'admin'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'pin', 'created_at', 'updated_at'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
	protected $appends = ['name'];

	/**
	 * getNameAttribute
	 *
	 * @return string
	 */
	public function getNameAttribute() {
		return $this->firstName . ' ' . $this->lastName;
	}
}
