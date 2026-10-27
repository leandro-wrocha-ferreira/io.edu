<?php

namespace app\domain\finance;

use app\domain\exceptions\ValidationException;
use app\domain\finance\value_objects\EncryptedSecret;
use DateTime;

/**
 * Domain entity representing encrypted integration credentials for a payment provider.
 */
final class PaymentCredential
{
	public const ENV_SANDBOX = 'sandbox';
	public const ENV_PRODUCTION = 'production';

	private ?int $id = null;
	private int $provider_id;
	private string $name;
	private string $environment;
	private EncryptedSecret $api_key;
	private ?EncryptedSecret $webhook_secret = null;
	private bool $is_active = true;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;

	/**
	 * Create a PaymentCredential domain entity.
	 *
	 * @param int $provider_id Provider ID
	 * @param string $name Credential label
	 * @param string $environment 'sandbox' or 'production'
	 * @param EncryptedSecret $api_key Encrypted API key value object
	 * @param EncryptedSecret|null $webhook_secret Encrypted webhook secret value object
	 * @param bool $is_active Active status
	 * @param int|null $id Credential ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @return self
	 * @throws ValidationException When validation fails
	 */
	public static function create(
		int $provider_id,
		string $name,
		string $environment,
		EncryptedSecret $api_key,
		?EncryptedSecret $webhook_secret = null,
		bool $is_active = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null
	): self
	{
		if ($provider_id <= 0) {
			throw new ValidationException("Provider ID must be greater than 0");
		}

		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Credential name cannot be empty");
		}

		$clean_env = strtolower(trim($environment));
		if (!in_array($clean_env, [self::ENV_SANDBOX, self::ENV_PRODUCTION], true)) {
			throw new ValidationException("Invalid credential environment: {$environment}");
		}

		$credential = new self();
		$credential->id = $id;
		$credential->provider_id = $provider_id;
		$credential->name = $clean_name;
		$credential->environment = $clean_env;
		$credential->api_key = $api_key;
		$credential->webhook_secret = $webhook_secret;
		$credential->is_active = $is_active;
		$credential->created_at = $created_at ?? new DateTime();
		$credential->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);

		return $credential;
	}

	/**
	 * Update credential attributes.
	 *
	 * @param string $name Credential name
	 * @param string $environment Environment (sandbox/production)
	 * @param EncryptedSecret|null $api_key If updated, encrypted API key value object
	 * @param EncryptedSecret|null $webhook_secret If updated, encrypted webhook secret value object
	 * @param bool $is_active Active status
	 * @return void
	 * @throws ValidationException When validation fails
	 */
	public function update_credentials(
		string $name,
		string $environment,
		?EncryptedSecret $api_key = null,
		?EncryptedSecret $webhook_secret = null,
		bool $is_active = true
	): void
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment credential name cannot be empty");
		}

		$clean_env = strtolower(trim($environment));
		if (!in_array($clean_env, [self::ENV_SANDBOX, self::ENV_PRODUCTION], true)) {
			throw new ValidationException("Invalid credential environment: {$environment}");
		}

		$this->name = $clean_name;
		$this->environment = $clean_env;
		if ($api_key !== null) {
			$this->api_key = $api_key;
		}
		if ($webhook_secret !== null) {
			$this->webhook_secret = $webhook_secret;
		}
		$this->is_active = $is_active;
		$this->updated_at = new DateTime();
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_provider_id(): int
	{
		return $this->provider_id;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function get_environment(): string
	{
		return $this->environment;
	}

	public function get_api_key(): EncryptedSecret
	{
		return $this->api_key;
	}

	public function get_webhook_secret(): ?EncryptedSecret
	{
		return $this->webhook_secret;
	}

	public function get_api_key_encrypted(): string
	{
		return $this->api_key->get_payload();
	}

	public function get_webhook_secret_encrypted(): ?string
	{
		return $this->webhook_secret?->get_payload();
	}

	public function is_active(): bool
	{
		return $this->is_active;
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
