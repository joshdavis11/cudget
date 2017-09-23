<?php

namespace App\Model;

use App\Notifications\ResetPassword;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @package App\Model
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {
	use Authenticatable, Authorizable, HasApiTokens, Notifiable;

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
		'email_verified' => 'bool',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['username', 'email', 'password', 'firstName', 'lastName', 'phone', 'admin', 'emailVerified'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'created_at', 'updated_at', 'email_verified'];

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

	/**
	 * Get the e-mail address where password reset links are sent.
	 *
	 * @return string
	 */
	public function getEmailForPasswordReset() {
		return $this->email;
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string $token
	 *
	 * @return void
	 */
	public function sendPasswordResetNotification($token) {
		$this->notify(new ResetPassword($token));
	}
}
