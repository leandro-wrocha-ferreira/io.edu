<?php

namespace tests\unit\domain\authorization;

use app\domain\authorization\constants\PermissionSlug;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for PermissionSlug Domain Constants.
 */
class PermissionSlugTest extends TestCase
{
	/**
	 * Test that permission constants have expected values.
	 *
	 * @return void
	 */
	public function test_constants_values(): void
	{
		$this->assertSame('dashboard.view', PermissionSlug::DASHBOARD_VIEW);
		$this->assertSame('users.view', PermissionSlug::USERS_VIEW);
		$this->assertSame('users.create', PermissionSlug::USERS_CREATE);
		$this->assertSame('users.edit', PermissionSlug::USERS_EDIT);
		$this->assertSame('users.toggle_status', PermissionSlug::USERS_TOGGLE_STATUS);
		$this->assertSame('users.delete', PermissionSlug::USERS_DELETE);
		$this->assertSame('roles.view', PermissionSlug::ROLES_VIEW);
		$this->assertSame('roles.create', PermissionSlug::ROLES_CREATE);
		$this->assertSame('roles.edit', PermissionSlug::ROLES_EDIT);
		$this->assertSame('roles.delete', PermissionSlug::ROLES_DELETE);
	}

	/**
	 * Test that ALL list contains all system permission slugs.
	 *
	 * @return void
	 */
	public function test_all_contains_expected_permissions(): void
	{
		$expected = [
			'dashboard.view',
			'users.view',
			'users.create',
			'users.edit',
			'users.toggle_status',
			'users.delete',
			'roles.view',
			'roles.create',
			'roles.edit',
			'roles.delete',
		];
		$this->assertSame($expected, PermissionSlug::ALL);
	}

	/**
	 * Test is_valid returns true for existing permissions and false for invalid slugs.
	 *
	 * @return void
	 */
	public function test_is_valid(): void
	{
		$this->assertTrue(PermissionSlug::is_valid('users.view'));
		$this->assertTrue(PermissionSlug::is_valid('roles.create'));
		$this->assertFalse(PermissionSlug::is_valid('invalid.permission'));
		$this->assertFalse(PermissionSlug::is_valid(''));
	}
}
