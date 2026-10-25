<?php

namespace app\domain\commercial\value_objects;

use app\domain\exceptions\ValidationException;

/**
 * Value Object representing the list of payment methods enabled for a product or configuration.
 */
final class EnabledPaymentMethods
{
	public const METHOD_PIX = 'pix';
	public const METHOD_CREDIT_CARD = 'credit_card';
	public const METHOD_BOLETO = 'boleto';

	public const ALL = [
		self::METHOD_PIX,
		self::METHOD_CREDIT_CARD,
		self::METHOD_BOLETO,
	];

	/**
	 * @var array<string>
	 */
	private array $methods;

	/**
	 * Constructor.
	 *
	 * @param array<string> $methods List of method slugs
	 * @throws ValidationException If invalid method provided
	 */
	public function __construct(array $methods = [])
	{
		$unique_methods = [];
		foreach ($methods as $method) {
			$clean = strtolower(trim((string) $method));
			if ($clean === '') {
				continue;
			}
			if (!in_array($clean, self::ALL, true)) {
				throw new ValidationException("Unsupported payment method: {$clean}");
			}
			if (!in_array($clean, $unique_methods, true)) {
				$unique_methods[] = $clean;
			}
		}

		$this->methods = array_values($unique_methods);
	}

	/**
	 * Create from JSON string or array.
	 *
	 * @param string|array<string> $input JSON or array
	 * @return self
	 */
	public static function from_mixed(string|array $input): self
	{
		if (is_array($input)) {
			return new self($input);
		}

		$decoded = json_decode($input, true);
		if (is_array($decoded)) {
			return new self($decoded);
		}

		return new self([]);
	}

	/**
	 * Get array of methods.
	 *
	 * @return array<string>
	 */
	public function get_methods(): array
	{
		return $this->methods;
	}

	/**
	 * Convert to array of method strings.
	 *
	 * @return array<string>
	 */
	public function to_array(): array
	{
		return $this->methods;
	}

	/**
	 * Check if a specific method is enabled (alias).
	 *
	 * @param string $method Method slug
	 * @return bool
	 */
	public function has(string $method): bool
	{
		return $this->has_method($method);
	}

	/**
	 * Check if a specific method is enabled.
	 *
	 * @param string $method Method slug
	 * @return bool
	 */
	public function has_method(string $method): bool
	{
		return in_array(strtolower(trim($method)), $this->methods, true);
	}

	/**
	 * Check if methods list is empty.
	 *
	 * @return bool
	 */
	public function is_empty(): bool
	{
		return empty($this->methods);
	}

	/**
	 * Check if current methods are a subset of allowed methods.
	 *
	 * @param array<string>|self $allowed Allowed methods
	 * @return bool
	 */
	public function is_subset_of(array|self $allowed): bool
	{
		$allowed_array = $allowed instanceof self ? $allowed->get_methods() : $allowed;
		foreach ($this->methods as $method) {
			if (!in_array($method, $allowed_array, true)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Convert to JSON string for database persistence.
	 *
	 * @return string
	 */
	public function to_json(): string
	{
		return json_encode($this->methods, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * String representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->to_json();
	}
}
