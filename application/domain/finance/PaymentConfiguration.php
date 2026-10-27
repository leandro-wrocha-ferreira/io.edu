<?php

namespace app\domain\finance;

use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a selectable consolidated financial configuration profile.
 */
final class PaymentConfiguration
{
	private ?int $id = null;
	private string $name;
	private int $payment_account_id;
	private array $allowed_methods;
	private ?string $description = null;
	private bool $is_active = true;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;
	private ?DateTime $deleted_at = null;
	private ?string $account_name = null;
	private ?string $account_identifier = null;
	private ?string $environment = null;
	private ?string $provider_name = null;
	private ?string $provider_slug = null;

	/**
	 * Create a PaymentConfiguration domain entity.
	 *
	 * @param string $name Configuration profile name
	 * @param int $payment_account_id Associated payment account ID
	 * @param array $allowed_methods Array of allowed payment methods
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @param int|null $id Configuration ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @param string|null $account_name Optional account name
	 * @param string|null $account_identifier Optional account identifier
	 * @param string|null $environment Optional account environment
	 * @param string|null $provider_name Optional provider name
	 * @param string|null $provider_slug Optional provider slug
	 * @return self
	 * @throws ValidationException When validation fails
	 */
	public static function create(
		string $name,
		int $payment_account_id,
		array $allowed_methods,
		?string $description = null,
		bool $is_active = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null,
		?string $account_name = null,
		?string $account_identifier = null,
		?string $environment = null,
		?string $provider_name = null,
		?string $provider_slug = null
	): self
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment configuration name cannot be empty");
		}

		if ($payment_account_id <= 0) {
			throw new ValidationException("Payment account ID must be greater than 0");
		}

		if (empty($allowed_methods)) {
			throw new ValidationException("Payment configuration must have at least one allowed payment method");
		}

		$config = new self();
		$config->id = $id;
		$config->name = $clean_name;
		$config->payment_account_id = $payment_account_id;
		$config->allowed_methods = array_values(array_unique($allowed_methods));
		$config->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$config->is_active = $is_active;
		$config->created_at = $created_at ?? new DateTime();
		$config->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$config->deleted_at = $deleted_at;
		$config->account_name = $account_name;
		$config->account_identifier = $account_identifier;
		$config->environment = $environment;
		$config->provider_name = $provider_name;
		$config->provider_slug = $provider_slug;

		return $config;
	}

	/**
	 * Update configuration profile attributes.
	 *
	 * @param string $name Profile name
	 * @param array $allowed_methods Array of allowed payment methods
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return void
	 * @throws ValidationException When validation fails
	 */
	public function update(string $name, array $allowed_methods, ?string $description = null, bool $is_active = true): void
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment configuration name cannot be empty");
		}

		if (empty($allowed_methods)) {
			throw new ValidationException("Payment configuration must have at least one allowed payment method");
		}

		$this->name = $clean_name;
		$this->allowed_methods = array_values(array_unique($allowed_methods));
		$this->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$this->is_active = $is_active;
		$this->updated_at = new DateTime();
	}

	public function supports_method(string $method): bool
	{
		return in_array(strtolower(trim($method)), $this->allowed_methods, true);
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function get_payment_account_id(): int
	{
		return $this->payment_account_id;
	}

	public function get_allowed_methods(): array
	{
		return $this->allowed_methods;
	}

	public function get_description(): ?string
	{
		return $this->description;
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

	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}

	public function get_account_name(): ?string
	{
		return $this->account_name;
	}

	public function get_account_identifier(): ?string
	{
		return $this->account_identifier;
	}

	public function get_environment(): ?string
	{
		return $this->environment;
	}

	public function get_provider_name(): ?string
	{
		return $this->provider_name;
	}

	public function get_provider_slug(): ?string
	{
		return $this->provider_slug;
	}

	public function set_account_details(
		?string $account_name,
		?string $account_identifier,
		?string $environment,
		?string $provider_name,
		?string $provider_slug
	): void
	{
		$this->account_name = $account_name;
		$this->account_identifier = $account_identifier;
		$this->environment = $environment;
		$this->provider_name = $provider_name;
		$this->provider_slug = $provider_slug;
	}
}
