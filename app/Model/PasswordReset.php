<?php

namespace App\Model;

class PasswordReset extends BaseModel {
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at'];
}