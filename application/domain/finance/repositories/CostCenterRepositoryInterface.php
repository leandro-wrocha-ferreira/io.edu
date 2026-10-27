<?php

namespace app\domain\finance\repositories;

use app\domain\finance\CostCenter;

/**
 * Repository interface for CostCenter.
 */
interface CostCenterRepositoryInterface
{
	/**
	 * Find a cost center by its ID.
	 *
	 * @param int $id
	 * @return CostCenter|null
	 */
	public function find_by_id(int $id): ?CostCenter;


	/**
	 * Find all active cost centers.
	 *
	 * @return array<CostCenter>
	 */
	public function find_all_active(): array;

	/**
	 * Save a cost center.
	 *
	 * @param CostCenter $cost_center
	 * @return CostCenter
	 */
	public function save(CostCenter $cost_center): CostCenter;

	/**
	 * Find cost centers with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Order column
	 * @param string $order_dir Order direction
	 * @param int|null $is_active Optional status filter
	 * @return array{data: array<CostCenter>, recordsFiltered: int}
	 */
	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $is_active = null
	): array;

	/**
	 * Count total non-deleted cost centers.
	 *
	 * @return int
	 */
	public function count_all(): int;

	/**
	 * Soft delete a cost center.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool;

	/**
	 * Check if cost center is referenced by any product.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function has_linked_products(int $id): bool;
}
