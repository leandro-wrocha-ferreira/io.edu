<?php

namespace app\domain\course\constants;

/**
 * Constants representing status for modules and lessons.
 */
final class ContentStatus
{
	public const DRAFT = 'draft';
	public const PUBLISHED = 'published';

	public const ALL = [
		self::DRAFT,
		self::PUBLISHED,
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
