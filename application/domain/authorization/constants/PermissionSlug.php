<?php

namespace app\domain\authorization\constants;

/**
 * Constants representing system permission slugs.
 */
final class PermissionSlug
{
	public const DASHBOARD_VIEW = 'dashboard.view';

	public const USERS_VIEW = 'users.view';
	public const USERS_CREATE = 'users.create';
	public const USERS_EDIT = 'users.edit';
	public const USERS_TOGGLE_STATUS = 'users.toggle_status';
	public const USERS_DELETE = 'users.delete';

	public const ROLES_VIEW = 'roles.view';
	public const ROLES_CREATE = 'roles.create';
	public const ROLES_EDIT = 'roles.edit';
	public const ROLES_DELETE = 'roles.delete';

	public const COURSES_VIEW = 'courses.view';
	public const COURSES_CREATE = 'courses.create';
	public const COURSES_EDIT = 'courses.edit';
	public const COURSES_DELETE = 'courses.delete';

	public const CATEGORIES_VIEW = 'categories.view';
	public const CATEGORIES_CREATE = 'categories.create';
	public const CATEGORIES_EDIT = 'categories.edit';
	public const CATEGORIES_DELETE = 'categories.delete';

	public const ALL = [
		self::DASHBOARD_VIEW,
		self::USERS_VIEW,
		self::USERS_CREATE,
		self::USERS_EDIT,
		self::USERS_TOGGLE_STATUS,
		self::USERS_DELETE,
		self::ROLES_VIEW,
		self::ROLES_CREATE,
		self::ROLES_EDIT,
		self::ROLES_DELETE,
		self::COURSES_VIEW,
		self::COURSES_CREATE,
		self::COURSES_EDIT,
		self::COURSES_DELETE,
		self::CATEGORIES_VIEW,
		self::CATEGORIES_CREATE,
		self::CATEGORIES_EDIT,
		self::CATEGORIES_DELETE,
	];

	/**
	 * Check if a given slug is a valid system permission.
	 *
	 * @param string $slug Slug to validate
	 * @return bool
	 */
	public static function is_valid(string $slug): bool
	{
		return in_array($slug, self::ALL, true);
	}
}
