<?php

namespace App\Model\Interfaces;

/**
 * Interface SortOrderInterface
 *
 * @package App\Model\Interfaces
 */
interface SortOrderInterface {
	/**
	 * Get the max sort_order for a given table that has a sort_order column
	 *
	 * @return int
	 */
	public function getMaxSortOrder();
}