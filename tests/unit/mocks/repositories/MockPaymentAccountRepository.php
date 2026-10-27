<?php

namespace tests\unit\mocks\repositories;

use app\domain\finance\PaymentAccount;
use app\domain\finance\repositories\PaymentAccountRepositoryInterface;
use app\models\dtos\PaymentAccountDatabase;
use app\models\mappers\PaymentAccountMapper;

/**
 * Shared mock repository for PaymentAccount tests.
 */
class MockPaymentAccountRepository implements PaymentAccountRepositoryInterface
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

	public function find_by_id(int $id): ?PaymentAccount
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id) {
				return PaymentAccountMapper::to_entity(new PaymentAccountDatabase($row));
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

		return PaymentAccountMapper::to_entities($matching);
	}

	public function save(PaymentAccount $account): PaymentAccount
	{
		if ($account->get_id() === null) {
			$data = PaymentAccountMapper::to_database_create($account);
			$data['id'] = $this->auto_increment++;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$this->rows[$data['id']] = $data;

			return PaymentAccountMapper::to_entity(new PaymentAccountDatabase($data));
		}

		$id = $account->get_id();
		$data = PaymentAccountMapper::to_database_update($account);
		$data['id'] = $id;
		$data['created_at'] = $this->rows[$id]['created_at'] ?? date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$id] = $data;

		return PaymentAccountMapper::to_entity(new PaymentAccountDatabase($data));
	}

	public function delete(int $id): bool
	{
		if (isset($this->rows[$id])) {
			$this->rows[$id]['status'] = PaymentAccount::STATUS_SUSPENDED;
			return true;
		}

		return false;
	}
}
