<?php

namespace App\Model;

/**
 * Class IncomeCategory
 *
 * @package App\Model
 */
class IncomeCategory extends BaseModel {
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['user_id', 'created_at', 'updated_at'];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at'];
}
