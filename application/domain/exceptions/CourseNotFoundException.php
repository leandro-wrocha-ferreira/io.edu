<?php

namespace app\domain\exceptions;

use Throwable;

/**
 * Exception thrown when a course entity is not found.
 *
 * Maps to HTTP 404 Not Found.
 */
class CourseNotFoundException extends NotFoundException
{
	/**
	 * Constructor.
	 *
	 * @param string $message Error message
	 * @param array<string, mixed> $errors Additional error details
	 * @param Throwable|null $previous Previous exception
	 */
	public function __construct(string $message = "Course not found", array $errors = [], ?Throwable $previous = null)
	{
		parent::__construct($message, $errors, $previous);
	}
}
