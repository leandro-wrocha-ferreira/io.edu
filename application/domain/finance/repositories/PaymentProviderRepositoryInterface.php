<?php

namespace app\domain\finance\repositories;

use app\domain\finance\PaymentProvider;

/**
 * Repository interface for PaymentProvider.
 */
interface PaymentProviderRepositoryInterface
{
	/**
	 * Find a payment provider by its ID.
	 *
	 * @param int $id
	 * @return PaymentProvider|null
	 */
	public function find_by_id(int $id): ?PaymentProvider;

	/**
	 * Find a payment provider by its unique slug.
	 *
	 * @param string $slug
	 * @return PaymentProvider|null
	 */
	public function find_by_slug(string $slug): ?PaymentProvider;

	/**
	 * Find all active payment providers.
	 *
	 * @return array<PaymentProvider>
	 */
	public function find_all_active(): array;
}
