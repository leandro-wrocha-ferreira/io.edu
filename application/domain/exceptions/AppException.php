<?php

namespace app\domain\exceptions;

/**
 * Base Application Exception.
 *
 * All domain and application-level exceptions inherit from this class.
 * Carries an HTTP status code and an optional array of specific field errors.
 */
class AppException extends \DomainException
{
    /** @var int HTTP status code */
    protected int $statusCode;

    /** @var array Detailed error list (e.g. field validation errors) */
    protected array $errors;

    /**
     * Constructor.
     *
     * @param string $message Exception message
     * @param int $statusCode HTTP status code (default: 400)
     * @param array $errors Field-level error list
     * @param \Throwable|null $previous Previous exception
     */
    public function __construct(string $message = "", int $statusCode = 400, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get detailed error list.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
