<?php

namespace app\domain\identity\constants;

/**
 * Constants representing system role slugs.
 */
final class RoleSlug
{
	public const ADMIN = 'admin';
	public const STUDENT = 'student';

	public const ALL = [
		self::ADMIN,
		self::STUDENT,
	];

	/**
	 * Check if a given slug is a valid system role.
	 *
	 * @param string $slug Slug to validate
	 * @return bool
	 */
	public static function is_valid(string $slug): bool
	{
		return in_array($slug, self::ALL, true);
	}
}
