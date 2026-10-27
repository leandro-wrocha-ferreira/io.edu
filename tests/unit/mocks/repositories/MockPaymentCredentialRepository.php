<?php

namespace tests\unit\mocks\repositories;

use app\domain\finance\PaymentCredential;
use app\domain\finance\repositories\PaymentCredentialRepositoryInterface;
use app\models\dtos\PaymentCredentialDatabase;
use app\models\mappers\PaymentCredentialMapper;

/**
 * Shared mock repository for PaymentCredential tests.
 */
class MockPaymentCredentialRepository implements PaymentCredentialRepositoryInterface
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

	public function find_by_id(int $id): ?PaymentCredential
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id) {
				return PaymentCredentialMapper::to_entity(new PaymentCredentialDatabase($row));
			}
		}

		return null;
	}

	public function find_by_provider_id(int $provider_id): array
	{
		$matching = [];
		foreach ($this->rows as $row) {
			if ($row['provider_id'] === $provider_id) {
				$matching[] = $row;
			}
		}

		return PaymentCredentialMapper::to_entities($matching);
	}

	public function save(PaymentCredential $credential): PaymentCredential
	{
		if ($credential->get_id() === null) {
			$data = PaymentCredentialMapper::to_database_create($credential);
			$data['id'] = $this->auto_increment++;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$this->rows[$data['id']] = $data;

			return PaymentCredentialMapper::to_entity(new PaymentCredentialDatabase($data));
		}

		$id = $credential->get_id();
		$data = PaymentCredentialMapper::to_database_update($credential);
		$data['id'] = $id;
		$data['created_at'] = $this->rows[$id]['created_at'] ?? date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$id] = $data;

		return PaymentCredentialMapper::to_entity(new PaymentCredentialDatabase($data));
	}

	public function delete(int $id): bool
	{
		if (isset($this->rows[$id])) {
			$this->rows[$id]['is_active'] = 0;
			return true;
		}

		return false;
	}
}
