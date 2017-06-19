<?php

namespace App\Model;

/**
 * Class SharedBudget
 *
 * @package App\Model
 */
class SharedBudget extends BaseModel {
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
	protected $hidden = ['created_at', 'updated_at', 'shared_datetime'];

	/**
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function budget() {
		return $this->belongsTo(Budget::class);
	}

	/**
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function user() {
		return $this->belongsTo(User::class);
	}
}
