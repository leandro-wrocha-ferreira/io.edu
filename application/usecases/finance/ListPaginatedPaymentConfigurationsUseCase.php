<?php

namespace app\usecases\finance;

use app\domain\finance\PaymentConfiguration;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;

/**
 * Use case for listing payment configurations with server-side pagination and search.
 */
class ListPaginatedPaymentConfigurationsUseCase
{
	/** @var PaymentConfigurationRepositoryInterface */
	private PaymentConfigurationRepositoryInterface $payment_config_repository;

	/**
	 * Constructor.
	 *
	 * @param PaymentConfigurationRepositoryInterface $payment_config_repository
	 */
	public function __construct(PaymentConfigurationRepositoryInterface $payment_config_repository)
	{
		$this->payment_config_repository = $payment_config_repository;
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
	 * @return array{data: array<PaymentConfiguration>, recordsFiltered: int, recordsTotal: int}
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
		$result = $this->payment_config_repository->find_paginated(
			$start,
			$length,
			$search,
			$order_col,
			$order_dir,
			$is_active
		);
		$total = $this->payment_config_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
