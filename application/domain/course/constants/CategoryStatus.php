<?php

namespace app\domain\course\constants;

/**
 * Constants representing category status.
 */
final class CategoryStatus
{
	public const ACTIVE = 'active';
	public const INACTIVE = 'inactive';

	public const ALL = [
		self::ACTIVE,
		self::INACTIVE,
	];

	/**
	 * Check if a given status string is valid.
	 *
	 * @param string $status
	 * @return bool
	 */
	public static function is_valid(string $status): bool
	{
		return in_array($status, self::ALL, true);
	}
}
