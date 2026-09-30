<?php

namespace tests\unit\models\mappers;

use app\domain\identity\Role;
use app\models\dtos\RoleDatabase;
use app\models\mappers\RoleMapper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for RoleMapper and RoleDatabase DTO.
 */
class RoleMapperTest extends TestCase
{
	/**
	 * Test mapping raw database DTO to Role entity.
	 *
	 * @return void
	 */
	public function test_to_entity(): void
	{
		$dto = new RoleDatabase([
			'id' => 1,
			'name' => 'Administrator',
			'slug' => 'admin',
			'description' => 'System admin',
			'created_at' => '2026-09-01 10:00:00',
			'updated_at' => '2026-09-02 11:00:00',
		]);

		$role = RoleMapper::to_entity($dto);

		$this->assertInstanceOf(Role::class, $role);
		$this->assertEquals(1, $role->get_id());
		$this->assertEquals('Administrator', $role->get_name());
		$this->assertEquals('admin', $role->get_slug());
		$this->assertEquals('System admin', $role->get_description());
		$this->assertNull(RoleMapper::to_entity(null));
	}

	/**
	 * Test mapping multiple raw database rows to Role entities.
	 *
	 * @return void
	 */
	public function test_to_entities(): void
	{
		$rows = [
			[
				'id' => 1,
				'name' => 'Admin',
				'slug' => 'admin',
				'description' => 'Admin role',
			],
			[
				'id' => 2,
				'name' => 'Student',
				'slug' => 'student',
				'description' => 'Student role',
			],
		];

		$entities = RoleMapper::to_entities($rows);

		$this->assertCount(2, $entities);
		$this->assertEquals('Admin', $entities[0]->get_name());
		$this->assertEquals('Student', $entities[1]->get_name());
		$this->assertEmpty(RoleMapper::to_entities([]));
	}

	/**
	 * Test mapping Role entity to database array for creation and update.
	 *
	 * @return void
	 */
	public function test_to_database_create_and_update(): void
	{
		$role = Role::create('Professor', 'teacher', 'Teacher role', 3);

		$create_data = RoleMapper::to_database_create($role);
		$this->assertEquals('Professor', $create_data['name']);
		$this->assertEquals('teacher', $create_data['slug']);
		$this->assertEquals('Teacher role', $create_data['description']);

		$update_data = RoleMapper::to_database_update($role);
		$this->assertEquals('Professor', $update_data['name']);
		$this->assertEquals('teacher', $update_data['slug']);
		$this->assertEquals('Teacher role', $update_data['description']);
	}
}
