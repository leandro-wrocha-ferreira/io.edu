<?php

namespace app\domain\course\value_objects;

use InvalidArgumentException;

/**
 * Value Object representing a course workload in hours.
 *
 * Immutable by design.
 */
final class Workload
{
	/**
	 * Total hours.
	 *
	 * @var int
	 */
	private int $hours;

	/**
	 * Constructor.
	 *
	 * @param int $hours
	 * @throws InvalidArgumentException When hours is negative
	 */
	public function __construct(int $hours)
	{
		if ($hours < 0) {
			throw new InvalidArgumentException("Workload hours cannot be negative");
		}
		$this->hours = $hours;
	}

	/**
	 * Get the workload in hours.
	 *
	 * @return int
	 */
	public function get_hours(): int
	{
		return $this->hours;
	}

	/**
	 * Return string representation.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->hours;
	}

	/**
	 * Check equality with another Workload.
	 *
	 * @param Workload $other
	 * @return bool
	 */
	public function equals(Workload $other): bool
	{
		return $this->hours === $other->get_hours();
	}
}
