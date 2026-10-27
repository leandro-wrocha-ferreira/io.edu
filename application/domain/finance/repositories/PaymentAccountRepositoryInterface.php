<?php

namespace app\domain\finance\repositories;

use app\domain\finance\PaymentAccount;

/**
 * Repository interface for PaymentAccount.
 */
interface PaymentAccountRepositoryInterface
{
	/**
	 * Find a payment account by its ID.
	 *
	 * @param int $id
	 * @return PaymentAccount|null
	 */
	public function find_by_id(int $id): ?PaymentAccount;

	/**
	 * Find accounts by provider ID.
	 *
	 * @param int $provider_id
	 * @return array<PaymentAccount>
	 */
	public function find_by_provider_id(int $provider_id): array;

	/**
	 * Save a payment account.
	 *
	 * @param PaymentAccount $account
	 * @return PaymentAccount
	 */
	public function save(PaymentAccount $account): PaymentAccount;

	/**
	 * Delete a payment account.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool;
}
