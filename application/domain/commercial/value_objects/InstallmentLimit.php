<?php

namespace app\domain\commercial\value_objects;

use app\domain\exceptions\ValidationException;

/**
 * Value Object representing the maximum allowed installments for one-time purchases (1 to 24).
 */
final class InstallmentLimit
{
	public const MIN = 1;
	public const MAX = 24;

	private int $value;

	/**
	 * Constructor.
	 *
	 * @param int $value Number of installments
	 * @throws ValidationException When installment limit is out of allowed range
	 */
	public function __construct(int $value)
	{
		if ($value < self::MIN || $value > self::MAX) {
			throw new ValidationException("Max installments must be between " . self::MIN . " and " . self::MAX);
		}

		$this->value = $value;
	}

	/**
	 * Factory from int.
	 *
	 * @param int $value Number of installments
	 * @return self
	 */
	public static function from_int(int $value): self
	{
		return new self($value);
	}

	/**
	 * Factory for single payment (à vista).
	 *
	 * @return self
	 */
	public static function single(): self
	{
		return new self(self::MIN);
	}

	/**
	 * Get installment limit value.
	 *
	 * @return int
	 */
	public function get_value(): int
	{
		return $this->value;
	}

	/**
	 * Get installment limit value (alias).
	 *
	 * @return int
	 */
	public function get_limit(): int
	{
		return $this->value;
	}

	/**
	 * Check if installment represents single payment (à vista).
	 *
	 * @return bool
	 */
	public function is_single_payment(): bool
	{
		return $this->value === self::MIN;
	}

	/**
	 * String representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->value;
	}
}
