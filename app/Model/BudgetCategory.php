<?php

namespace App\Model;

use App\Model\Interfaces\SortOrderInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class BudgetCategory
 *
 * @package App\Model
 */
class BudgetCategory extends BaseModel implements SortOrderInterface {
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
		'budget_id' => 'int',
		'sort_order' => 'int',
	];

	/**
	 * Get the max sort_order
	 *
	 * @return mixed
	 */
	public function getMaxSortOrder() {
		return DB::table($this->getTable())->where('budget_id', '=', $this->budgetId)->max('sort_order');
	}

	/**
	 * Get the budget category rows
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function budgetCategoryRows() {
		return $this->hasMany(BudgetCategoryRow::class);
	}
}
