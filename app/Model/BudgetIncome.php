<?php

namespace App\Model;

/**
 * Class BudgetIncome
 *
 * @package App\Model
 */
class BudgetIncome extends BaseModel {
	/**
	 * The database table name
	 *
	 * @var string
	 */
	protected $table = 'budget_income';

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = ['budgetId', 'incomeId'];

	/**
	 * Get the income
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function income() {
		return $this->belongsTo(Income::class);
	}

	/**
	 * Get the user
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function budget() {
		return $this->belongsTo(Budget::class);
	}
}
