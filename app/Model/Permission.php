<?php

namespace App\Model;

/**
 * Class Permission
 *
 * @package App\Model
 * @property int $id
 * @property string $name
 * @property string $definition
 */
class Permission extends BaseModel {
	const DEFINITION_IMPORT = 'import';
	const DEFINITION_BUDGET_TEMPLATES = 'budgetTemplates';
	const DEFINITION_COLOR_SCHEME = 'colorScheme';
	const DEFINITION_BUDGET_SHARING = 'budgetSharing';
	const DEFINITION_ACCOUNTS = 'accounts';

	/**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
	protected $casts = ['id' => 'int',];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name',];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['created_at', 'updated_at',];
}