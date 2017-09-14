<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Class BudgetCategoryRow
 *
 * @package App\Model
 */
class BudgetCategoryRow extends BaseModel {
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['created_at', 'updated_at'];

	/**
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'estimated' => 'float',
		'sort_order' => 'int',
	];

	/**
	 * Get the max sort_order
	 *
	 * @return mixed
	 */
	public function getMaxSortOrder() {
		return DB::table($this->getTable())->where('budget_category_id', '=', $this->budgetCategoryId)->max('sort_order');
	}

    /**
	 * Get the budget category row expenses
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function budgetCategoryRowExpenses() {
		return $this->hasMany(BudgetCategoryRowExpense::class);
	}

	/**
	 * Get the budget category it belongs to
	 *
	 * @return BelongsTo
	 */
	public function budgetCategory(): BelongsTo {
    	return $this->belongsTo(BudgetCategory::class);
	}
}
