<?php

namespace app\domain\finance\repositories;

use app\domain\finance\PaymentConfiguration;

/**
 * Repository interface for PaymentConfiguration.
 */
interface PaymentConfigurationRepositoryInterface
{
	/**
	 * Find a payment configuration by its ID.
	 *
	 * @param int $id
	 * @return PaymentConfiguration|null
	 */
	public function find_by_id(int $id): ?PaymentConfiguration;

	/**
	 * Find all active payment configurations.
	 *
	 * @return array<PaymentConfiguration>
	 */
	public function find_all_active(): array;

	/**
	 * Save a payment configuration.
	 *
	 * @param PaymentConfiguration $configuration
	 * @return PaymentConfiguration
	 */
	public function save(PaymentConfiguration $configuration): PaymentConfiguration;

	/**
	 * Find payment configurations with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Order column
	 * @param string $order_dir Order direction
	 * @param int|null $is_active Optional status filter
	 * @return array{data: array<PaymentConfiguration>, recordsFiltered: int}
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
	 * Count total non-deleted payment configurations.
	 *
	 * @return int
	 */
	public function count_all(): int;

	/**
	 * Soft delete a payment configuration.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool;

	/**
	 * Check if payment configuration is referenced by any product.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function has_linked_products(int $id): bool;
}
