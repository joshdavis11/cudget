<?php

namespace App\Model;
/**
 * Class PlaidData
 *
 * @property int id
 * @property int userId
 * @property string accessToken
 * @property string itemId
 * @property string lastRun
 * @package App\Model
 */
class PlaidData extends BaseModel {
	/**
	 * @var string
	 */
	protected $table = 'plaid_data';

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'access_token', 'item_id',];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at',];
}