<?php

namespace tests\unit\mocks\repositories;

use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;
use app\models\dtos\CostCenterDatabase;
use app\models\mappers\CostCenterMapper;

/**
 * Shared mock repository for CostCenter tests.
 */
class MockCostCenterRepository implements CostCenterRepositoryInterface
{
	/**
	 * Simulated raw database rows.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private array $rows = [];

	/**
	 * Auto-increment counter.
	 *
	 * @var int
	 */
	private int $auto_increment = 1;

	public function find_by_id(int $id): ?CostCenter
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return CostCenterMapper::to_entity(new CostCenterDatabase($row));
			}
		}

		return null;
	}


	public function find_all_active(): array
	{
		$active_rows = [];
		foreach ($this->rows as $row) {
			if (!empty($row['is_active']) && empty($row['deleted_at'])) {
				$active_rows[] = $row;
			}
		}

		return CostCenterMapper::to_entities($active_rows);
	}

	public function save(CostCenter $cost_center): CostCenter
	{
		if ($cost_center->get_id() === null) {
			$data = CostCenterMapper::to_database_create($cost_center);
			$data['id'] = $this->auto_increment++;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$data['deleted_at'] = null;
			$this->rows[$data['id']] = $data;

			return CostCenterMapper::to_entity(new CostCenterDatabase($data));
		}

		$id = $cost_center->get_id();
		$data = CostCenterMapper::to_database_update($cost_center);
		$data['id'] = $id;
		$data['created_at'] = $this->rows[$id]['created_at'] ?? date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$id] = $data;

		return CostCenterMapper::to_entity(new CostCenterDatabase($data));
	}

	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $is_active = null
	): array
	{
		$filtered_rows = [];
		foreach ($this->rows as $row) {
			if (!empty($row['deleted_at'])) {
				continue;
			}
			if ($is_active !== null && (int)$row['is_active'] !== $is_active) {
				continue;
			}
			if ($search !== '') {
				$found = stripos($row['name'], $search) !== false
					|| (!empty($row['description']) && stripos($row['description'], $search) !== false)
					|| (string) $row['id'] === $search;
				if (!$found) {
					continue;
				}
			}
			$filtered_rows[] = $row;
		}

		$total_filtered = count($filtered_rows);
		$sliced_rows = array_slice($filtered_rows, $start, $length);

		return [
			'data' => CostCenterMapper::to_entities($sliced_rows),
			'recordsFiltered' => $total_filtered,
		];
	}

	public function count_all(): int
	{
		$count = 0;
		foreach ($this->rows as $row) {
			if (empty($row['deleted_at'])) {
				$count++;
			}
		}

		return $count;
	}

	public function delete(int $id): bool
	{
		if (isset($this->rows[$id])) {
			$this->rows[$id]['deleted_at'] = date('Y-m-d H:i:s');
			return true;
		}

		return false;
	}

	/** @var array<int, bool> */
	private array $linked_products = [];

	public function set_has_linked_products(int $id, bool $has_linked): void
	{
		$this->linked_products[$id] = $has_linked;
	}

	public function has_linked_products(int $id): bool
	{
		return !empty($this->linked_products[$id]);
	}
}
