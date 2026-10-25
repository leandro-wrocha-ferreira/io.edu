<?php

namespace app\domain\commercial\value_objects;

use app\domain\exceptions\ValidationException;

/**
 * Value Object representing the recurring billing interval for subscriptions.
 */
final class SubscriptionInterval
{
	public const MONTHLY = 'monthly';
	public const QUARTERLY = 'quarterly';
	public const SEMIANNUAL = 'semiannual';
	public const YEARLY = 'yearly';

	public const ALL = [
		self::MONTHLY,
		self::QUARTERLY,
		self::SEMIANNUAL,
		self::YEARLY,
	];

	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $value Interval string
	 * @throws ValidationException When interval is invalid
	 */
	public function __construct(string $value)
	{
		$clean = strtolower(trim($value));
		if (!in_array($clean, self::ALL, true)) {
			throw new ValidationException("Invalid subscription interval: {$value}");
		}

		$this->value = $clean;
	}

	/**
	 * Factory from string.
	 *
	 * @param string $value Interval string
	 * @return self
	 */
	public static function from_string(string $value): self
	{
		return new self($value);
	}

	public static function monthly(): self
	{
		return new self(self::MONTHLY);
	}

	public static function quarterly(): self
	{
		return new self(self::QUARTERLY);
	}

	public static function semiannual(): self
	{
		return new self(self::SEMIANNUAL);
	}

	public static function yearly(): self
	{
		return new self(self::YEARLY);
	}

	/**
	 * Get raw interval value.
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
