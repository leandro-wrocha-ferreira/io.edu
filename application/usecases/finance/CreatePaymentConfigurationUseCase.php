<?php

namespace app\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\CredentialEncryptionInterface;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\PaymentCredential;
use app\domain\finance\repositories\PaymentAccountRepositoryInterface;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\domain\finance\repositories\PaymentCredentialRepositoryInterface;
use app\domain\finance\repositories\PaymentProviderRepositoryInterface;
use app\domain\finance\value_objects\EncryptedSecret;
use app\infrastructure\database\TransactionInterface;

/**
 * Use case for creating a payment configuration along with its credential and account in a single transaction.
 */
class CreatePaymentConfigurationUseCase
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

	/** @var TransactionInterface */
	private TransactionInterface $transaction;

	/**
	 * Constructor.
	 *
	 * @param PaymentConfigurationRepositoryInterface $payment_config_repository
	 * @param PaymentAccountRepositoryInterface $payment_account_repository
	 * @param PaymentCredentialRepositoryInterface $payment_credential_repository
	 * @param PaymentProviderRepositoryInterface $payment_provider_repository
	 * @param CredentialEncryptionInterface $encryption_service
	 * @param TransactionInterface $transaction
	 */
	public function __construct(
		PaymentConfigurationRepositoryInterface $payment_config_repository,
		PaymentAccountRepositoryInterface $payment_account_repository,
		PaymentCredentialRepositoryInterface $payment_credential_repository,
		PaymentProviderRepositoryInterface $payment_provider_repository,
		CredentialEncryptionInterface $encryption_service,
		TransactionInterface $transaction
	)
	{
		$this->payment_config_repository = $payment_config_repository;
		$this->payment_account_repository = $payment_account_repository;
		$this->payment_credential_repository = $payment_credential_repository;
		$this->payment_provider_repository = $payment_provider_repository;
		$this->encryption_service = $encryption_service;
		$this->transaction = $transaction;
	}

	/**
	 * Execute payment configuration creation.
	 *
	 * @param string $name Configuration profile name
	 * @param int $provider_id Payment provider ID
	 * @param string $environment Environment (sandbox/production)
	 * @param string $api_key Plaintext API key
	 * @param array $allowed_methods Array of allowed payment methods
	 * @param string|null $webhook_secret Optional webhook secret
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return PaymentConfiguration
	 * @throws NotFoundException When provider not found
	 * @throws ValidationException When invariants are violated
	 */
	public function execute(
		string $name,
		int $provider_id,
		string $environment,
		string $api_key,
		array $allowed_methods,
		?string $webhook_secret = null,
		?string $description = null,
		bool $is_active = true
	): PaymentConfiguration
	{
		$provider = $this->payment_provider_repository->find_by_id($provider_id);
		if ($provider === null || !$provider->is_active()) {
			throw new NotFoundException("Payment provider not found");
		}

		$clean_api_key = trim($api_key);
		if ($clean_api_key === '') {
			throw new ValidationException("API key cannot be empty");
		}

		if (empty($allowed_methods)) {
			throw new ValidationException("Payment configuration must have at least one allowed payment method");
		}

		$api_key_secret = EncryptedSecret::from_plain_text($clean_api_key, $this->encryption_service);
		$webhook_secret_vo = null;
		if ($webhook_secret !== null && trim($webhook_secret) !== '') {
			$webhook_secret_vo = EncryptedSecret::from_plain_text(trim($webhook_secret), $this->encryption_service);
		}

		return $this->transaction->run(function () use (
			$name,
			$provider_id,
			$environment,
			$api_key_secret,
			$webhook_secret_vo,
			$allowed_methods,
			$description,
			$is_active
		): PaymentConfiguration {
			// 1. Create and persist the secure payment credential
			$credential = PaymentCredential::create(
				$provider_id,
				$name . ' - Credencial',
				$environment,
				$api_key_secret,
				$webhook_secret_vo,
				true
			);
			$saved_credential = $this->payment_credential_repository->save($credential);

			// 2. Create and persist the payment account tied to the credential
			$account = PaymentAccount::create(
				$provider_id,
				$saved_credential->get_id(),
				$name . ' - Conta',
				$environment,
				null,
				null,
				PaymentAccount::STATUS_ACTIVE
			);
			$saved_account = $this->payment_account_repository->save($account);

			// 3. Create and persist the payment configuration profile linked to the account
			$configuration = PaymentConfiguration::create(
				$name,
				$saved_account->get_id(),
				$allowed_methods,
				$description,
				$is_active
			);

			return $this->payment_config_repository->save($configuration);
		});
	}
}
