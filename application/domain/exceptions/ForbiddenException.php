<?php

namespace app\domain\exceptions;

/**
 * Exception thrown when an authenticated user does not have permission to perform an action.
 *
 * Maps to HTTP 403 Forbidden.
 */
class ForbiddenException extends AppException
{
    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param array $errors Additional error details
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "Acesso negado", array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 403, $errors, $previous);
    }
}
