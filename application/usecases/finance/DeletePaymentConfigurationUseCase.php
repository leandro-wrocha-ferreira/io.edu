<?php

namespace app\usecases\finance;

use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;

/**
 * Use case for soft deleting a payment configuration.
 */
class DeletePaymentConfigurationUseCase
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
	 * Execute soft delete.
	 *
	 * @param int $id Payment configuration ID
	 * @return bool
	 * @throws NotFoundException When payment configuration not found
	 * @throws ConflictException When payment configuration has linked products
	 */
	public function execute(int $id): bool
	{
		$configuration = $this->payment_config_repository->find_by_id($id);
		if ($configuration === null) {
			throw new NotFoundException("Payment configuration not found");
		}

		if ($this->payment_config_repository->has_linked_products($id)) {
			throw new ConflictException("Cannot delete payment configuration with linked products");
		}

		return $this->payment_config_repository->delete($id);
	}
}
