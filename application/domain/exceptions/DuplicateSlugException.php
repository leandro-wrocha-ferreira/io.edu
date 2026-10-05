<?php

namespace app\domain\exceptions;

use Throwable;

/**
 * Exception thrown when a duplicate slug is detected.
 *
 * Maps to HTTP 409 Conflict.
 */
class DuplicateSlugException extends ConflictException
{
	/**
	 * Constructor.
	 *
	 * @param string $message Error message
	 * @param array<string, mixed> $errors Additional error details
	 * @param Throwable|null $previous Previous exception
	 */
	public function __construct(string $message = "Duplicate slug already exists", array $errors = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $errors, $previous);
	}
}
