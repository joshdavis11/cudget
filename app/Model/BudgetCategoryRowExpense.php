<?php

namespace App\Model;

/**
 * Class BudgetCategoryRowExpense
 *
 * @package App\Model
 */
class BudgetCategoryRowExpense extends BaseModel {
	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'budget_category_row_id' => 'int',
		'expense_id' => 'int',
	];

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
	 * Get the expense
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function expense() {
		return $this->belongsTo(Expense::class);
	}
}
