<?php

namespace app\domain\identity;

class Email
{
    private $value;

    public function __construct(string $email)
    {
        $trimmed = trim($email);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido: {$email}");
        }
        $this->value = strtolower($trimmed);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === (string) $other;
    }
}
