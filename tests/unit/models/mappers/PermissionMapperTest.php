<?php

namespace tests\unit\models\mappers;

use app\domain\authorization\Permission;
use app\models\dtos\PermissionDatabase;
use app\models\mappers\PermissionMapper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for PermissionMapper and PermissionDatabase DTO.
 */
class PermissionMapperTest extends TestCase
{
	/**
	 * Test mapping raw database DTO to Permission entity.
	 *
	 * @return void
	 */
	public function test_to_entity(): void
	{
		$dto = new PermissionDatabase([
			'id' => 1,
			'name' => 'View Users',
			'slug' => 'users.view',
			'description' => 'Permission to view users',
			'created_at' => '2026-09-01 10:00:00',
			'updated_at' => '2026-09-02 11:00:00',
		]);

		$permission = PermissionMapper::to_entity($dto);

		$this->assertInstanceOf(Permission::class, $permission);
		$this->assertEquals(1, $permission->get_id());
		$this->assertEquals('View Users', $permission->get_name());
		$this->assertEquals('users.view', $permission->get_slug());
		$this->assertEquals('Permission to view users', $permission->get_description());
		$this->assertNull(PermissionMapper::to_entity(null));
	}

	/**
	 * Test mapping multiple raw database rows to Permission entities.
	 *
	 * @return void
	 */
	public function test_to_entities(): void
	{
		$rows = [
			[
				'id' => 1,
				'name' => 'Create User',
				'slug' => 'users.create',
				'description' => 'Can create users',
			],
			[
				'id' => 2,
				'name' => 'Edit User',
				'slug' => 'users.edit',
				'description' => 'Can edit users',
			],
		];

		$entities = PermissionMapper::to_entities($rows);

		$this->assertCount(2, $entities);
		$this->assertEquals('Create User', $entities[0]->get_name());
		$this->assertEquals('Edit User', $entities[1]->get_name());
		$this->assertEmpty(PermissionMapper::to_entities([]));
	}

	/**
	 * Test mapping Permission entity to database array for creation and update.
	 *
	 * @return void
	 */
	public function test_to_database_create_and_update(): void
	{
		$permission = Permission::create('Delete User', 'users.delete', 'Can delete users', 4);

		$create_data = PermissionMapper::to_database_create($permission);
		$this->assertEquals('Delete User', $create_data['name']);
		$this->assertEquals('users.delete', $create_data['slug']);
		$this->assertEquals('Can delete users', $create_data['description']);

		$update_data = PermissionMapper::to_database_update($permission);
		$this->assertEquals('Delete User', $update_data['name']);
		$this->assertEquals('users.delete', $update_data['slug']);
		$this->assertEquals('Can delete users', $update_data['description']);
	}
}
