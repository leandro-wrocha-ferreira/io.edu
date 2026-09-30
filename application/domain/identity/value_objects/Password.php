<?php

namespace app\domain\identity\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing a user password.
 *
 * Automatically manages password hashing and verification.
 * Immutable by design.
 */
class Password
{
	/**
	 * Hashed password string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * Accepts either a plain-text password (which is automatically hashed)
	 * or an existing bcrypt hash.
	 *
	 * @param string $password Plain text password or existing hash
	 * @throws InvalidArgumentException If password is empty
	 */
	public function __construct(string $password)
	{
		$trimmed = trim($password);
		if ($trimmed === '') {
			throw new InvalidArgumentException("Password cannot be empty");
		}

		$info = password_get_info($trimmed);
		if ($info['algo'] !== null && $info['algo'] !== 0) {
			$this->value = $trimmed;
		} else {
			$this->value = password_hash($trimmed, PASSWORD_BCRYPT);
		}
	}

	/**
	 * Create a Password VO from an existing hash (e.g. from database).
	 *
	 * @param string $hash Hashed password
	 * @return self
	 */
	public static function from_hash(string $hash): self
	{
		return new self($hash);
	}

	/**
	 * Create a Password VO from plain text.
	 *
	 * @param string $plain_text Plain text password
	 * @return self
	 */
	public static function from_plain_text(string $plain_text): self
	{
		return new self($plain_text);
	}

	/**
	 * Verify a plain text password against this password hash.
	 *
	 * @param string $plain_text Plain text password to verify
	 * @return bool
	 */
	public function verify(string $plain_text): bool
	{
		return password_verify($plain_text, $this->value);
	}

	/**
	 * Get the hashed string representation.
	 *
	 * @return string
	 */
	public function get_value(): string
	{
		return $this->value;
	}

	/**
	 * Convert the password to string (returns the hash).
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Compare equality with another Password VO.
	 *
	 * @param Password $other
	 * @return bool
	 */
	public function equals(Password $other): bool
	{
		return $this->value === (string) $other;
	}
}
