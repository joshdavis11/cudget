<?php

namespace App\Model;

/**
 * Class UserToken
 *
 * @package App\Model
 */
class UserToken extends BaseModel {
	const TYPE_PASSWORD_RESET = 'password_reset';
	const TYPE_ACTIVATION = 'activation';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'token', 'expires',];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at'];

    /**
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function user() {
		return $this->belongsTo(User::class);
	}
}