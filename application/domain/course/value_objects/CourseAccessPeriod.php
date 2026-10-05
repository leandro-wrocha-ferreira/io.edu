<?php

namespace app\domain\course\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing a course access period configuration.
 *
 * Encapsulates rules for lifetime vs limited_time access.
 * Immutable by design.
 */
final class CourseAccessPeriod
{
	public const TYPE_LIFETIME = 'lifetime';
	public const TYPE_LIMITED_TIME = 'limited_time';

	public const ALL_TYPES = [
		self::TYPE_LIFETIME,
		self::TYPE_LIMITED_TIME,
	];

	/**
	 * Access period type.
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * Number of access days (when type is limited_time).
	 *
	 * @var int|null
	 */
	private ?int $days = null;

	/**
	 * Constructor.
	 *
	 * @param string $type
	 * @param int|null $days
	 * @throws InvalidArgumentException When type is invalid or days is not positive for limited_time
	 */
	public function __construct(string $type, ?int $days = null)
	{
		$trimmed_type = trim(strtolower($type));
		if (!in_array($trimmed_type, self::ALL_TYPES, true)) {
			throw new InvalidArgumentException("Invalid access period type: {$type}");
		}

		if ($trimmed_type === self::TYPE_LIMITED_TIME) {
			if ($days === null || $days <= 0) {
				throw new InvalidArgumentException("Limited time access requires access days to be greater than 0");
			}
			$this->days = $days;
		} else {
			$this->days = null;
		}

		$this->type = $trimmed_type;
	}

	/**
	 * Create lifetime access period.
	 *
	 * @return self
	 */
	public static function lifetime(): self
	{
		return new self(self::TYPE_LIFETIME);
	}

	/**
	 * Create limited time access period.
	 *
	 * @param int $days
	 * @return self
	 */
	public static function limited_time(int $days): self
	{
		return new self(self::TYPE_LIMITED_TIME, $days);
	}

	/**
	 * Check if access is lifetime.
	 *
	 * @return bool
	 */
	public function is_lifetime(): bool
	{
		return $this->type === self::TYPE_LIFETIME;
	}

	/**
	 * Check if access is limited time.
	 *
	 * @return bool
	 */
	public function is_limited_time(): bool
	{
		return $this->type === self::TYPE_LIMITED_TIME;
	}

	/**
	 * Get the type value.
	 *
	 * @return string
	 */
	public function get_type(): string
	{
		return $this->type;
	}

	/**
	 * Get access days.
	 *
	 * @return int|null
	 */
	public function get_days(): ?int
	{
		return $this->days;
	}

	/**
	 * Return string representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->is_limited_time()
			? "limited_time:{$this->days}"
			: self::TYPE_LIFETIME;
	}

	/**
	 * Check equality with another CourseAccessPeriod.
	 *
	 * @param CourseAccessPeriod $other
	 * @return bool
	 */
	public function equals(CourseAccessPeriod $other): bool
	{
		return $this->type === $other->get_type() && $this->days === $other->get_days();
	}
}
