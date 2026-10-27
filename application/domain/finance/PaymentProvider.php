<?php

namespace app\domain\finance;

use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing an acquirer payment provider (e.g. Asaas, Pagar.me).
 */
final class PaymentProvider
{
	private ?int $id = null;
	private string $name;
	private string $slug;
	private array $supported_methods;
	private bool $is_active = true;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;

	/**
	 * Create a PaymentProvider domain entity.
	 *
	 * @param string $name Provider name
	 * @param string $slug Provider unique slug
	 * @param array $supported_methods Supported payment methods
	 * @param bool $is_active Active status
	 * @param int|null $id Provider ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @return self
	 * @throws ValidationException When validation fails
	 */
	public static function create(
		string $name,
		string $slug,
		array $supported_methods,
		bool $is_active = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null
	): self
	{
		$clean_name = trim($name);
		if ($clean_name === '') {
			throw new ValidationException("Payment provider name cannot be empty");
		}

		$clean_slug = strtolower(trim($slug));
		if ($clean_slug === '') {
			throw new ValidationException("Payment provider slug cannot be empty");
		}

		$provider = new self();
		$provider->id = $id;
		$provider->name = $clean_name;
		$provider->slug = $clean_slug;
		$provider->supported_methods = array_values(array_unique($supported_methods));
		$provider->is_active = $is_active;
		$provider->created_at = $created_at ?? new DateTime();
		$provider->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);

		return $provider;
	}

	public function supports_method(string $method): bool
	{
		return in_array(strtolower(trim($method)), $this->supported_methods, true);
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_name(): string
	{
		return $this->name;
	}

	public function get_slug(): string
	{
		return $this->slug;
	}

	public function get_supported_methods(): array
	{
		return $this->supported_methods;
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
