<?php

namespace App\Model;

class Budget extends BaseModel {
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
	 * Cast items to certain types
	 *
	 * @var array
	 */
	protected $casts = [
		'income' => 'float',
		'userId' => 'int',
		'template' => 'bool',
	];

	/**
	 * Get the budget categories
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function budgetCategories() {
		return $this->hasMany(BudgetCategory::class);
	}

	/**
	 * Get the budget income
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function budgetIncome() {
		return $this->hasMany(BudgetIncome::class);
	}

	/**
	 * sharedBudget
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sharedBudget() {
		return $this->hasMany(SharedBudget::class);
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
