<?php

namespace App\Model;

/**
 * Class UserPermission
 *
 * @package App\Model
 */
class UserPermission extends BaseModel {
	/**
	 * @var array
	 */
	static protected $rules = [
		'user_id' => 'required|integer',
		'section' => 'required|string',
		'permission' => 'required|string|in:add,delete,update,view',
		'access' => 'required|boolean',
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'access' => 'bool',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['userId', 'section', 'permission', 'access'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at'];

    /**
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function user() {
		return $this->belongsTo(User::class);
	}
}
