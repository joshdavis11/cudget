<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class PlaidData
 *
 * @property int id
 * @property int userId
 * @property string accessToken
 * @property string itemId
 * @property string institutionId
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
	protected $hidden = ['access_token', 'item_id', 'last_run', 'created_at', 'updated_at',];

	/**
	 * The Plaid Accounts
	 *
	 * @return HasMany
	 */
	public function plaidAccount(): HasMany {
    	return $this->hasMany(PlaidAccount::class);
	}
}