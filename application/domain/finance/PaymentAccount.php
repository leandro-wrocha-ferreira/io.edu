<?php

namespace app\domain\finance;

use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a receiver account/subaccount in an acquirer.
 */
final class PaymentAccount
{
	public const STATUS_ACTIVE = 'active';
	public const STATUS_PENDING = 'pending';
	public const STATUS_SUSPENDED = 'suspended';

	private ?int $id = null;
	private int $provider_id;
	private int $credential_id;
	private string $name;
	private string $environment;
	private ?string $account_identifier = null;
	private ?string $document_number = null;
	private string $status;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;

	/**
	 * Create a PaymentAccount domain entity.
	 *
	 * @param int $provider_id Provider ID
	 * @param int $credential_id Credential ID
	 * @param string $name Account name
	 * @param string $environment 'sandbox' or 'production'
	 * @param string|null $account_identifier Gateway account/wallet ID
	 * @param string|null $document_number CPF/CNPJ
	 * @param string $status Account status
	 * @param int|null $id Account ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @return self
	 * @throws ValidationException When validation fails
	 */
	public static function create(
		int $provider_id,
		int $credential_id,
		string $name,
		string $environment,
		?string $account_identifier = null,
		?string $document_number = null,
		string $status = self::STATUS_ACTIVE,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null
	): self
	{
		if ($provider_id <= 0) {
			throw new ValidationException("Provider ID must be greater than 0");
		}

		if ($credential_id <= 0) {
			throw new ValidationException("Credential ID must be greater than 0");
		}

		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment account name cannot be empty");
		}

		$clean_identifier = $account_identifier !== null && trim($account_identifier) !== '' ? trim($account_identifier) : null;

		$clean_env = strtolower(trim($environment));
		if (!in_array($clean_env, [PaymentCredential::ENV_SANDBOX, PaymentCredential::ENV_PRODUCTION], true)) {
			throw new ValidationException("Invalid account environment: {$environment}");
		}

		$clean_status = strtolower(trim($status));
		if (!in_array($clean_status, [self::STATUS_ACTIVE, self::STATUS_PENDING, self::STATUS_SUSPENDED], true)) {
			throw new ValidationException("Invalid account status: {$status}");
		}

		$account = new self();
		$account->id = $id;
		$account->provider_id = $provider_id;
		$account->credential_id = $credential_id;
		$account->name = $clean_name;
		$account->environment = $clean_env;
		$account->account_identifier = $clean_identifier;
		$account->document_number = $document_number !== null && trim($document_number) !== '' ? trim($document_number) : null;
		$account->status = $clean_status;
		$account->created_at = $created_at ?? new DateTime();
		$account->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);

		return $account;
	}

	/**
	 * Update payment account attributes.
	 *
	 * @param string $name Account name
	 * @param string $environment Environment (sandbox/production)
	 * @param string|null $account_identifier Gateway account/wallet identifier
	 * @param string|null $document_number CPF/CNPJ
	 * @param string $status Status
	 * @return void
	 * @throws ValidationException When validation fails
	 */
	public function update(
		string $name,
		string $environment,
		?string $account_identifier = null,
		?string $document_number = null,
		string $status = self::STATUS_ACTIVE
	): void
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment account name cannot be empty");
		}

		$clean_identifier = $account_identifier !== null && trim($account_identifier) !== '' ? trim($account_identifier) : null;

		$clean_env = strtolower(trim($environment));
		if (!in_array($clean_env, [PaymentCredential::ENV_SANDBOX, PaymentCredential::ENV_PRODUCTION], true)) {
			throw new ValidationException("Invalid account environment: {$environment}");
		}

		$clean_status = strtolower(trim($status));
		if (!in_array($clean_status, [self::STATUS_ACTIVE, self::STATUS_PENDING, self::STATUS_SUSPENDED], true)) {
			throw new ValidationException("Invalid account status: {$status}");
		}

		$this->name = $clean_name;
		$this->environment = $clean_env;
		$this->account_identifier = $clean_identifier;
		$this->document_number = $document_number !== null && trim($document_number) !== '' ? trim($document_number) : null;
		$this->status = $clean_status;
		$this->updated_at = new DateTime();
	}

	public function is_active(): bool
	{
		return $this->status === self::STATUS_ACTIVE;
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_provider_id(): int
	{
		return $this->provider_id;
	}

	public function get_credential_id(): int
	{
		return $this->credential_id;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function get_environment(): string
	{
		return $this->environment;
	}

	public function get_account_identifier(): ?string
	{
		return $this->account_identifier;
	}

	public function get_document_number(): ?string
	{
		return $this->document_number;
	}

	public function get_status(): string
	{
		return $this->status;
	}

	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}
}
