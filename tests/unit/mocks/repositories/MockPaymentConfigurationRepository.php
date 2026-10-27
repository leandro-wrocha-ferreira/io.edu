<?php

namespace tests\unit\mocks\repositories;

use app\domain\finance\PaymentConfiguration;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\models\dtos\PaymentConfigurationDatabase;
use app\models\mappers\PaymentConfigurationMapper;

/**
 * Shared mock repository for PaymentConfiguration tests.
 */
class MockPaymentConfigurationRepository implements PaymentConfigurationRepositoryInterface
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

	public function find_by_id(int $id): ?PaymentConfiguration
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return PaymentConfigurationMapper::to_entity(new PaymentConfigurationDatabase($row));
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

		return PaymentConfigurationMapper::to_entities($active_rows);
	}

	public function save(PaymentConfiguration $configuration): PaymentConfiguration
	{
		if ($configuration->get_id() === null) {
			$data = PaymentConfigurationMapper::to_database_create($configuration);
			$data['id'] = $this->auto_increment++;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$data['deleted_at'] = null;
			$this->rows[$data['id']] = $data;

			return PaymentConfigurationMapper::to_entity(new PaymentConfigurationDatabase($data));
		}

		$id = $configuration->get_id();
		$data = PaymentConfigurationMapper::to_database_update($configuration);
		$data['id'] = $id;
		$data['created_at'] = $this->rows[$id]['created_at'] ?? date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$id] = $data;

		return PaymentConfigurationMapper::to_entity(new PaymentConfigurationDatabase($data));
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
					|| (!empty($row['description']) && stripos($row['description'], $search) !== false);
				if (!$found) {
					continue;
				}
			}
			$filtered_rows[] = $row;
		}

		$total_filtered = count($filtered_rows);
		$sliced_rows = array_slice($filtered_rows, $start, $length);

		return [
			'data' => PaymentConfigurationMapper::to_entities($sliced_rows),
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
