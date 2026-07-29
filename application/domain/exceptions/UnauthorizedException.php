<?php

namespace app\domain\exceptions;

/**
 * Exception thrown when authentication is required or credentials are invalid.
 *
 * Maps to HTTP 401 Unauthorized.
 */
class UnauthorizedException extends AppException
{
    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param array $errors Additional error details
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "Não autorizado", array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $errors, $previous);
    }
}
