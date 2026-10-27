<?php

namespace app\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\CredentialEncryptionInterface;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\PaymentCredential;
use app\domain\finance\PaymentProvider;
use app\domain\finance\repositories\PaymentAccountRepositoryInterface;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\domain\finance\repositories\PaymentCredentialRepositoryInterface;
use app\domain\finance\repositories\PaymentProviderRepositoryInterface;

/**
 * Use case for retrieving full details of a payment configuration including account and provider.
 */
class GetPaymentConfigurationDetailUseCase
{
	/** @var PaymentConfigurationRepositoryInterface */
	private PaymentConfigurationRepositoryInterface $payment_config_repository;

	/** @var PaymentAccountRepositoryInterface */
	private PaymentAccountRepositoryInterface $payment_account_repository;

	/** @var PaymentCredentialRepositoryInterface */
	private PaymentCredentialRepositoryInterface $payment_credential_repository;

	/** @var PaymentProviderRepositoryInterface */
	private PaymentProviderRepositoryInterface $payment_provider_repository;

	/** @var CredentialEncryptionInterface */
	private CredentialEncryptionInterface $encryption_service;

	/**
	 * Constructor.
	 *
	 * @param PaymentConfigurationRepositoryInterface $payment_config_repository
	 * @param PaymentAccountRepositoryInterface $payment_account_repository
	 * @param PaymentCredentialRepositoryInterface $payment_credential_repository
	 * @param PaymentProviderRepositoryInterface $payment_provider_repository
	 * @param CredentialEncryptionInterface $encryption_service
	 */
	public function __construct(
		PaymentConfigurationRepositoryInterface $payment_config_repository,
		PaymentAccountRepositoryInterface $payment_account_repository,
		PaymentCredentialRepositoryInterface $payment_credential_repository,
		PaymentProviderRepositoryInterface $payment_provider_repository,
		CredentialEncryptionInterface $encryption_service
	)
	{
		$this->payment_config_repository = $payment_config_repository;
		$this->payment_account_repository = $payment_account_repository;
		$this->payment_credential_repository = $payment_credential_repository;
		$this->payment_provider_repository = $payment_provider_repository;
		$this->encryption_service = $encryption_service;
	}

	/**
	 * Execute detailed retrieval.
	 *
	 * @param int $id Payment configuration identifier
	 * @return array{configuration: PaymentConfiguration, account: PaymentAccount, credential: PaymentCredential, provider: PaymentProvider, masked_api_key: string}
	 * @throws NotFoundException When any entity is not found
	 */
	public function execute(int $id): array
	{
		$configuration = $this->payment_config_repository->find_by_id($id);
		if ($configuration === null) {
			throw new NotFoundException("Payment configuration not found");
		}

		$account = $this->payment_account_repository->find_by_id($configuration->get_payment_account_id());
		if ($account === null) {
			throw new NotFoundException("Payment account not found");
		}

		$credential = $this->payment_credential_repository->find_by_id($account->get_credential_id());
		if ($credential === null) {
			throw new NotFoundException("Payment credential not found");
		}

		$provider = $this->payment_provider_repository->find_by_id($account->get_provider_id());
		if ($provider === null) {
			throw new NotFoundException("Payment provider not found");
		}

		$masked_api_key = '****';
		try {
			$decrypted = $credential->get_api_key()->decrypt($this->encryption_service);
			if (strlen($decrypted) > 8) {
				$masked_api_key = substr($decrypted, 0, 4) . '****' . substr($decrypted, -4);
			} else {
				$masked_api_key = '****' . substr($decrypted, -2);
			}
		} catch (\Throwable $exception) {
			$masked_api_key = '**** (encrypted)';
		}

		return [
			'configuration' => $configuration,
			'account' => $account,
			'credential' => $credential,
			'provider' => $provider,
			'masked_api_key' => $masked_api_key,
		];
	}
}
