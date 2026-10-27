<?php

namespace app\usecases\finance;

use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for listing cost centers with server-side pagination and search.
 */
class ListPaginatedCostCentersUseCase
{
	/** @var CostCenterRepositoryInterface */
	private CostCenterRepositoryInterface $cost_center_repository;

	/**
	 * Constructor.
	 *
	 * @param CostCenterRepositoryInterface $cost_center_repository
	 */
	public function __construct(CostCenterRepositoryInterface $cost_center_repository)
	{
		$this->cost_center_repository = $cost_center_repository;
	}

	/**
	 * Execute paginated search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Column name
	 * @param string $order_dir Direction (ASC/DESC)
	 * @param int|null $is_active Optional status filter
	 * @return array{data: array<CostCenter>, recordsFiltered: int, recordsTotal: int}
	 */
	public function execute(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $is_active = null
	): array
	{
		$result = $this->cost_center_repository->find_paginated(
			$start,
			$length,
			$search,
			$order_col,
			$order_dir,
			$is_active
		);
		$total = $this->cost_center_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
