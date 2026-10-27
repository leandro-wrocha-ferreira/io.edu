<?php

namespace tests\unit\mocks\repositories;

use app\domain\finance\PaymentProvider;
use app\domain\finance\repositories\PaymentProviderRepositoryInterface;
use app\models\dtos\PaymentProviderDatabase;
use app\models\mappers\PaymentProviderMapper;

/**
 * Shared mock repository for PaymentProvider tests.
 */
class MockPaymentProviderRepository implements PaymentProviderRepositoryInterface
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

	public function find_by_id(int $id): ?PaymentProvider
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id) {
				return PaymentProviderMapper::to_entity(new PaymentProviderDatabase($row));
			}
		}

		return null;
	}

	public function find_by_slug(string $slug): ?PaymentProvider
	{
		$clean_slug = strtolower(trim($slug));
		foreach ($this->rows as $row) {
			if ($row['slug'] === $clean_slug) {
				return PaymentProviderMapper::to_entity(new PaymentProviderDatabase($row));
			}
		}

		return null;
	}

	public function find_all_active(): array
	{
		$active_rows = [];
		foreach ($this->rows as $row) {
			if (!empty($row['is_active'])) {
				$active_rows[] = $row;
			}
		}

		return PaymentProviderMapper::to_entities($active_rows);
	}

	public function create(PaymentProvider $provider): PaymentProvider
	{
		$data = PaymentProviderMapper::to_database_create($provider);
		$data['id'] = $this->auto_increment++;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$data['id']] = $data;

		return PaymentProviderMapper::to_entity(new PaymentProviderDatabase($data));
	}

	public function save(PaymentProvider $provider): PaymentProvider
	{
		return $this->create($provider);
	}
}
