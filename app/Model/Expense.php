<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Expense
 *
 * @property int id
 * @property int userId
 * @property string datetime
 * @property string description
 * @property int expenseCategoryId
 * @property float amount
 * @property string isoCurrencyCode
 * @property string transactionId
 * @property string accountId
 * @package App\Model
 */
class Expense extends BaseModel {
	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'amount' => 'float',
		'expense_category_id' => 'int',
		'user_id' => 'int',
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
	protected $hidden = ['created_at', 'updated_at', 'user_id'];

    /**
	 * Get the expense category
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function expenseCategory() {
		return $this->belongsTo(ExpenseCategory::class);
	}

	/**
	 * The budget category row expense
	 *
	 * @return HasOne
	 */
	public function budgetCategoryRowExpense() {
    	return $this->hasOne(BudgetCategoryRowExpense::class);
	}
}
