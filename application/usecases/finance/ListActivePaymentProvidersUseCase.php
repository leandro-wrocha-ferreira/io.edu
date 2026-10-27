<?php

namespace app\usecases\finance;

use app\domain\finance\PaymentProvider;
use app\domain\finance\repositories\PaymentProviderRepositoryInterface;

/**
 * Use case for listing active payment providers.
 */
class ListActivePaymentProvidersUseCase
{
	/** @var PaymentProviderRepositoryInterface */
	private PaymentProviderRepositoryInterface $payment_provider_repository;

	/**
	 * Constructor.
	 *
	 * @param PaymentProviderRepositoryInterface $payment_provider_repository
	 */
	public function __construct(PaymentProviderRepositoryInterface $payment_provider_repository)
	{
		$this->payment_provider_repository = $payment_provider_repository;
	}

	/**
	 * Execute use case.
	 *
	 * @return array<PaymentProvider>
	 */
	public function execute(): array
	{
		return $this->payment_provider_repository->find_all_active();
	}
}
