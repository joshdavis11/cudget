<?php

namespace App\Model;

/**
 * Class AutoImportAccount
 *
 * @package App\Model
 */
class AutoImportAccount extends BaseModel {
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['created_at', 'updated_at'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at'];

	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'int',
		'description' => 'int',
		'date' => 'int',
		'amount' => 'int',
	];
}
