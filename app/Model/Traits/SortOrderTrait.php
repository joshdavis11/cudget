<?php
namespace App\Model\Traits;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Trait SortOrder
 *
 * @package App\Model\Traits
 */
trait SortOrderTrait {
	/**
	 * Get the max sort_order for a given table that has a sort_order column
	 *
	 * @param Closure $where The optional where closure to perform any where clauses
	 *
	 * @return int The max sort order
	 */
	public function getMaxSortOrder() {
		return DB::table($this->getTable())->max('sort_order');
	}
}