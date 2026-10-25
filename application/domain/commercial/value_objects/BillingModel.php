<?php

namespace app\domain\commercial\value_objects;

use app\domain\exceptions\ValidationException;

/**
 * Value Object representing a product billing model.
 */
final class BillingModel
{
	public const ONE_TIME = 'one_time';
	public const SUBSCRIPTION = 'subscription';

	public const ALL = [
		self::ONE_TIME,
		self::SUBSCRIPTION,
	];

	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $value Billing model value
	 * @throws ValidationException When model is not valid
	 */
	public function __construct(string $value)
	{
		$clean = strtolower(trim($value));
		if (!in_array($clean, self::ALL, true)) {
			throw new ValidationException("Invalid billing model: {$value}");
		}

		$this->value = $clean;
	}

	/**
	 * Factory from string.
	 *
	 * @param string $value
	 * @return self
	 */
	public static function from_string(string $value): self
	{
		return new self($value);
	}

	/**
	 * Factory for one-time billing model.
	 *
	 * @return self
	 */
	public static function one_time(): self
	{
		return new self(self::ONE_TIME);
	}

	/**
	 * Factory for subscription billing model.
	 *
	 * @return self
	 */
	public static function subscription(): self
	{
		return new self(self::SUBSCRIPTION);
	}

	/**
	 * Check if model is one-time purchase.
	 *
	 * @return bool
	 */
	public function is_one_time(): bool
	{
		return $this->value === self::ONE_TIME;
	}

	/**
	 * Check if model is recurring subscription.
	 *
	 * @return bool
	 */
	public function is_subscription(): bool
	{
		return $this->value === self::SUBSCRIPTION;
	}

	/**
	 * Get raw string value.
	 *
	 * @return string
	 */
	public function get_value(): string
	{
		return $this->value;
	}

	/**
	 * String representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->value;
	}
}
