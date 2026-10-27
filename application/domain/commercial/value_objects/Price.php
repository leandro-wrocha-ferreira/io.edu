<?php

namespace app\domain\commercial\value_objects;

use app\domain\exceptions\ValidationException;

/**
 * Value Object representing a monetary price strictly in integer cents.
 */
final class Price
{
	/**
	 * Price in integer cents (e.g., 19990 for R$ 199,90).
	 *
	 * @var int
	 */
	private int $cents;

	/**
	 * ISO 4217 Currency code (default BRL).
	 *
	 * @var string
	 */
	private string $currency;

	/**
	 * Private constructor.
	 *
	 * @param int $cents Price in cents
	 * @param string $currency Currency code
	 * @throws ValidationException If cents is negative
	 */
	private function __construct(int $cents, string $currency = 'BRL')
	{
		if ($cents < 0) {
			throw new ValidationException("Price cannot be negative");
		}

		$this->cents = $cents;
		$this->currency = strtoupper(trim($currency));
	}

	/**
	 * Create Price from integer cents.
	 *
	 * @param int $cents Value in cents
	 * @param string $currency Currency code
	 * @return self
	 */
	public static function from_cents(int $cents, string $currency = 'BRL'): self
	{
		return new self($cents, $currency);
	}

	/**
	 * Create Price from formatted or raw string without floating point arithmetic.
	 *
	 * @param string $formatted_value Formatted or raw string e.g. "199,90" or "R$ 199,90" or "199.90"
	 * @param string $currency Currency code
	 * @return self
	 * @throws ValidationException If string format is invalid
	 */
	public static function from_string(string $formatted_value, string $currency = 'BRL'): self
	{
		$cleaned = trim($formatted_value);
		// Strip currency symbols and spaces
		$cleaned = preg_replace('/[^\d.,]/', '', $cleaned);

		if ($cleaned === '') {
			return new self(0, $currency);
		}

		// Normalize thousand separators and decimals
		if (str_contains($cleaned, ',') && str_contains($cleaned, '.')) {
			// e.g. "1.234,56" or "1,234.56"
			$comma_pos = strrpos($cleaned, ',');
			$dot_pos = strrpos($cleaned, '.');
			if ($comma_pos > $dot_pos) {
				// Brazilian format: 1.234,56
				$cleaned = str_replace('.', '', $cleaned);
				$parts = explode(',', $cleaned);
			} else {
				// US format: 1,234.56
				$cleaned = str_replace(',', '', $cleaned);
				$parts = explode('.', $cleaned);
			}
		} elseif (str_contains($cleaned, ',')) {
			// e.g. "199,90"
			$parts = explode(',', $cleaned);
		} elseif (str_contains($cleaned, '.')) {
			// e.g. "199.90"
			$parts = explode('.', $cleaned);
		} else {
			// Whole number e.g. "199"
			$parts = [$cleaned, '00'];
		}

		$units = (int) ($parts[0] !== '' ? $parts[0] : 0);
		$decimals = isset($parts[1]) ? substr(str_pad($parts[1], 2, '0'), 0, 2) : '00';
		$cents = ($units * 100) + (int) $decimals;

		return new self($cents, $currency);
	}

	/**
	 * Get the price in integer cents.
	 *
	 * @return int
	 */
	public function get_cents(): int
	{
		return $this->cents;
	}

	/**
	 * Get the currency code.
	 *
	 * @return string
	 */
	public function get_currency(): string
	{
		return $this->currency;
	}

	/**
	 * Check if price is zero (free).
	 *
	 * @return bool
	 */
	public function is_zero(): bool
	{
		return $this->cents === 0;
	}

	/**
	 * Compare equality with another Price instance.
	 *
	 * @param Price $other Other price
	 * @return bool
	 */
	public function equals(Price $other): bool
	{
		return $this->cents === $other->get_cents() && $this->currency === $other->get_currency();
	}

	/**
	 * Convert to float strictly for UI display formatting.
	 *
	 * @return float
	 */
	public function to_float_display(): float
	{
		return (float) ($this->cents / 100);
	}

	/**
	 * Format price for human display (e.g., "R$ 199,90" or "Gratuito").
	 *
	 * @return string
	 */
	public function format(): string
	{
		if ($this->is_zero()) {
			return 'Gratuito';
		}

		$units = intdiv($this->cents, 100);
		$remainder = $this->cents % 100;
		$formatted_number = number_format($units, 0, '', '.') . ',' . str_pad((string) $remainder, 2, '0', STR_PAD_LEFT);

		return 'R$ ' . $formatted_number;
	}

	/**
	 * String representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->cents;
	}
}
