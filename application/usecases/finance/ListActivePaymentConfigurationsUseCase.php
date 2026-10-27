<?php

namespace app\usecases\finance;

use app\domain\finance\PaymentConfiguration;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;

/**
 * Use case for listing active payment configurations.
 */
class ListActivePaymentConfigurationsUseCase
{
	/** @var PaymentConfigurationRepositoryInterface */
	private PaymentConfigurationRepositoryInterface $payment_configuration_repository;

	/**
	 * Constructor.
	 *
	 * @param PaymentConfigurationRepositoryInterface $payment_configuration_repository
	 */
	public function __construct(PaymentConfigurationRepositoryInterface $payment_configuration_repository)
	{
		$this->payment_configuration_repository = $payment_configuration_repository;
	}

	/**
	 * Execute use case.
	 *
	 * @return array<PaymentConfiguration>
	 */
	public function execute(): array
	{
		return $this->payment_configuration_repository->find_all_active();
	}
}
