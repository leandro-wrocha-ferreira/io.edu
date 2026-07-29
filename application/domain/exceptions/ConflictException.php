<?php

namespace app\domain\exceptions;

/**
 * Exception thrown when an operation conflicts with the current state of a resource.
 *
 * Example: Attempting to delete a role that has active users assigned to it.
 * Maps to HTTP 409 Conflict.
 */
class ConflictException extends AppException
{
    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param array $errors Additional error details
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "Operação em conflito com o estado atual", array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 409, $errors, $previous);
    }
}
