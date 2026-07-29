<?php

namespace app\domain\exceptions;

/**
 * Exception thrown when a requested domain entity or resource is not found.
 *
 * Maps to HTTP 404 Not Found.
 */
class NotFoundException extends AppException
{
    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param array $errors Additional error details
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "Recurso não encontrado", array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, $errors, $previous);
    }
}
