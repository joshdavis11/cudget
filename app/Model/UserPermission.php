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
		'permission_id' => 'required|integer',
	];

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'permission_id' => 'int',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['userId', 'permissionId',];

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

	/**
	 * Get the permission
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function permission() {
    	return $this->belongsTo(Permission::class);
	}
}
