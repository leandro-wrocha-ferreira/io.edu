<?php

namespace app\domain\exceptions;

/**
 * Exception thrown when domain validation or business rules fail.
 *
 * Maps to HTTP 422 Unprocessable Entity.
 */
class ValidationException extends AppException
{
    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param array $errors Field-specific validation errors
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "Dados inválidos", array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 422, $errors, $previous);
    }
}
