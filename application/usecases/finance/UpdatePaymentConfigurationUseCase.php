<?php

namespace app\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\CredentialEncryptionInterface;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\repositories\PaymentAccountRepositoryInterface;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\domain\finance\repositories\PaymentCredentialRepositoryInterface;
use app\domain\finance\value_objects\EncryptedSecret;
use app\infrastructure\database\TransactionInterface;

/**
 * Use case for updating a payment configuration and its associated account/credentials in a single transaction.
 */
class UpdatePaymentConfigurationUseCase
{
	/** @var PaymentConfigurationRepositoryInterface */
	private PaymentConfigurationRepositoryInterface $payment_config_repository;

	/** @var PaymentAccountRepositoryInterface */
	private PaymentAccountRepositoryInterface $payment_account_repository;

	/** @var PaymentCredentialRepositoryInterface */
	private PaymentCredentialRepositoryInterface $payment_credential_repository;

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
	 * @param CredentialEncryptionInterface $encryption_service
	 * @param TransactionInterface $transaction
	 */
	public function __construct(
		PaymentConfigurationRepositoryInterface $payment_config_repository,
		PaymentAccountRepositoryInterface $payment_account_repository,
		PaymentCredentialRepositoryInterface $payment_credential_repository,
		CredentialEncryptionInterface $encryption_service,
		TransactionInterface $transaction
	)
	{
		$this->payment_config_repository = $payment_config_repository;
		$this->payment_account_repository = $payment_account_repository;
		$this->payment_credential_repository = $payment_credential_repository;
		$this->encryption_service = $encryption_service;
		$this->transaction = $transaction;
	}

	/**
	 * Execute payment configuration update.
	 *
	 * @param int $id Payment configuration ID
	 * @param string $name Configuration profile name
	 * @param string $environment Environment (sandbox/production)
	 * @param array $allowed_methods Array of allowed payment methods
	 * @param string|null $api_key Optional new plaintext API key
	 * @param string|null $webhook_secret Optional new webhook secret
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return PaymentConfiguration
	 * @throws NotFoundException When any entity is not found
	 * @throws ValidationException When validation fails
	 */
	public function execute(
		int $id,
		string $name,
		string $environment,
		array $allowed_methods,
		?string $api_key = null,
		?string $webhook_secret = null,
		?string $description = null,
		bool $is_active = true
	): PaymentConfiguration
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

		if (empty($allowed_methods)) {
			throw new ValidationException("Payment configuration must have at least one allowed payment method");
		}

		$api_key_secret = null;
		if ($api_key !== null && trim($api_key) !== '') {
			$api_key_secret = EncryptedSecret::from_plain_text(trim($api_key), $this->encryption_service);
		}

		$webhook_secret_vo = null;
		if ($webhook_secret !== null && trim($webhook_secret) !== '') {
			$webhook_secret_vo = EncryptedSecret::from_plain_text(trim($webhook_secret), $this->encryption_service);
		}

		return $this->transaction->run(function () use (
			$configuration,
			$account,
			$credential,
			$name,
			$environment,
			$api_key_secret,
			$webhook_secret_vo,
			$allowed_methods,
			$description,
			$is_active
		): PaymentConfiguration {
			// 1. Update credential records
			$credential->update_credentials(
				$name . ' - Credencial',
				$environment,
				$api_key_secret,
				$webhook_secret_vo,
				true
			);
			$this->payment_credential_repository->save($credential);

			// 2. Update account records
			$account->update(
				$name . ' - Conta',
				$environment,
				null,
				null,
				PaymentAccount::STATUS_ACTIVE
			);
			$this->payment_account_repository->save($account);

			// 3. Update configuration records
			$configuration->update(
				$name,
				$allowed_methods,
				$description,
				$is_active
			);

			return $this->payment_config_repository->save($configuration);
		});
	}
}
