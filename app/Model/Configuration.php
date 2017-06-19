<?php

namespace App\Model;

/**
 * Class Configuration
 *
 * @package App\Model
 */
class Configuration extends BaseModel {
	/**
	 * The table name
	 *
	 * @var string
	 */
	protected $table = 'configuration';

	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = ['user_id' => 'int'];

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
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function user() {
		return $this->belongsTo(User::class);
	}
}
