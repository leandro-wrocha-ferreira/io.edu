<?php

namespace app\domain\course\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing course status.
 *
 * Immutable by design.
 */
final class CourseStatus
{
	public const DRAFT = 'draft';
	public const ACTIVE = 'active';
	public const ARCHIVED = 'archived';

	public const ALL = [
		self::DRAFT,
		self::ACTIVE,
		self::ARCHIVED,
	];

	/**
	 * Status value.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $status
	 * @throws InvalidArgumentException If status is invalid
	 */
	public function __construct(string $status)
	{
		$trimmed = trim(strtolower($status));
		if (!in_array($trimmed, self::ALL, true)) {
			throw new InvalidArgumentException("Invalid course status: {$status}");
		}
		$this->value = $trimmed;
	}

	/**
	 * Create draft status.
	 *
	 * @return self
	 */
	public static function draft(): self
	{
		return new self(self::DRAFT);
	}

	/**
	 * Create active status.
	 *
	 * @return self
	 */
	public static function active(): self
	{
		return new self(self::ACTIVE);
	}

	/**
	 * Create archived status.
	 *
	 * @return self
	 */
	public static function archived(): self
	{
		return new self(self::ARCHIVED);
	}

	/**
	 * Check if status is draft.
	 *
	 * @return bool
	 */
	public function is_draft(): bool
	{
		return $this->value === self::DRAFT;
	}

	/**
	 * Check if status is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool
	{
		return $this->value === self::ACTIVE;
	}

	/**
	 * Check if status is archived.
	 *
	 * @return bool
	 */
	public function is_archived(): bool
	{
		return $this->value === self::ARCHIVED;
	}

	/**
	 * Get the status string value.
	 *
	 * @return string
	 */
	public function get_value(): string
	{
		return $this->value;
	}

	/**
	 * Return string representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Check equality with another CourseStatus.
	 *
	 * @param CourseStatus $other
	 * @return bool
	 */
	public function equals(CourseStatus $other): bool
	{
		return $this->value === (string) $other;
	}
}
