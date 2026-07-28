<?php

namespace app\domain\identity;

/**
 * Value Object representing an email address.
 *
 * Validates email format on construction, trims whitespace,
 * and normalizes to lowercase. Immutable by design.
 */
class Email
{
    private string $value;

    /**
     * Constructor.
     *
     * @param string $email Email address to validate and store
     * @throws \InvalidArgumentException If the email is invalid
     */
    public function __construct(string $email)
    {
        $trimmed = trim($email);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: {$email}");
        }
        $this->value = strtolower($trimmed);
    }

    /**
     * Get the string representation of the email.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check equality with another Email Value Object.
     *
     * @param Email $other Email to compare
     * @return bool
     */
    public function equals(Email $other): bool
    {
        return $this->value === (string) $other;
    }
}
