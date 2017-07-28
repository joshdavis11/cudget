<?php

namespace App\Model;

/**
 * Class Permission
 *
 * @package App\Model
 */
class Permission extends BaseModel {
	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = ['id' => 'int',];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name',];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at',];
}