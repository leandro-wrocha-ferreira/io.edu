<?php

namespace tests\unit\domain\identity;

use app\domain\identity\constants\RoleSlug;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for RoleSlug Domain Constants.
 */
class RoleSlugTest extends TestCase
{
	/**
	 * Test that role constants have expected values.
	 *
	 * @return void
	 */
	public function test_constants_values(): void
	{
		$this->assertSame('admin', RoleSlug::ADMIN);
		$this->assertSame('student', RoleSlug::STUDENT);
	}

	/**
	 * Test that ALL list contains all system role slugs.
	 *
	 * @return void
	 */
	public function test_all_contains_expected_roles(): void
	{
		$this->assertSame(['admin', 'student'], RoleSlug::ALL);
	}

	/**
	 * Test is_valid returns true for existing roles and false for invalid slugs.
	 *
	 * @return void
	 */
	public function test_is_valid(): void
	{
		$this->assertTrue(RoleSlug::is_valid('admin'));
		$this->assertTrue(RoleSlug::is_valid('student'));
		$this->assertFalse(RoleSlug::is_valid('invalid'));
		$this->assertFalse(RoleSlug::is_valid('admin-master'));
	}
}
