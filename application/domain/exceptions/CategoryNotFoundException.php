<?php

namespace app\domain\exceptions;

use Throwable;

/**
 * Exception thrown when a category entity is not found.
 *
 * Maps to HTTP 404 Not Found.
 */
class CategoryNotFoundException extends NotFoundException
{
	/**
	 * Constructor.
	 *
	 * @param string $message Error message
	 * @param array<string, mixed> $errors Additional error details
	 * @param Throwable|null $previous Previous exception
	 */
	public function __construct(string $message = "Category not found", array $errors = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $errors, $previous);
	}
}
