<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PlaidAccount
 *
 * @property int id
 * @property int plaidDataId
 * @property int userId
 * @property string accountId
 * @property string name
 * @property string mask
 * @property string type
 * @property string subtype
 * @property bool includeInUpdates
 * @package App\Model
 */
class PlaidAccount extends BaseModel {
	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = [
		'id' => 'int',
		'plaid_data_id' => 'int',
		'user_id' => 'int',
		'include_in_updates' => 'bool',
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['plaid_data_id', 'user_id', 'account_id', 'name', 'mask', 'type', 'subtype', 'include_in_updates',];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['account_id', 'created_at', 'updated_at',];

	/**
	 * The Plaid Data
	 *
	 * @return BelongsTo
	 */
	public function plaidData(): BelongsTo {
    	return $this->belongsTo(PlaidData::class);
	}
}