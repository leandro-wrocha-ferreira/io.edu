<?php

namespace app\domain\finance\repositories;

use app\domain\finance\PaymentCredential;

/**
 * Repository interface for PaymentCredential.
 */
interface PaymentCredentialRepositoryInterface
{
	/**
	 * Find a payment credential by its ID.
	 *
	 * @param int $id
	 * @return PaymentCredential|null
	 */
	public function find_by_id(int $id): ?PaymentCredential;

	/**
	 * Find credentials by provider ID.
	 *
	 * @param int $provider_id
	 * @return array<PaymentCredential>
	 */
	public function find_by_provider_id(int $provider_id): array;

	/**
	 * Save a payment credential.
	 *
	 * @param PaymentCredential $credential
	 * @return PaymentCredential
	 */
	public function save(PaymentCredential $credential): PaymentCredential;

	/**
	 * Delete a payment credential.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool;
}
