<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Income
 *
 * @property int id
 * @property int userId
 * @property string datetime
 * @property string description
 * @property int incomeCategoryId
 * @property float amount
 * @property string isoCurrencyCode
 * @property string transactionId
 * @property string accountId
 * @package App\Model
 */
class Income extends BaseModel {
	/**
	 * The table name
	 *
	 * @var string
	 */
    protected $table = 'income';

	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'amount' => 'float'
	];

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

    /**
	 * Get the income category
	 *
	 * @return BelongsTo
	 */
    public function incomeCategory() {
		return $this->belongsTo(IncomeCategory::class);
	}

	/**
	 * The budget income
	 *
	 * @return HasOne
	 */
	public function budgetIncome() {
    	return $this->hasOne(BudgetIncome::class);
	}
}
