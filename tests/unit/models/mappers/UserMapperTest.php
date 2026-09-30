<?php

namespace tests\unit\models\mappers;

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\domain\identity\value_objects\Password;
use app\models\dtos\UserDatabase;
use app\models\mappers\UserMapper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for UserMapper and UserDatabase DTO.
 */
class UserMapperTest extends TestCase
{
	/**
	 * Test mapping raw database DTO to User entity.
	 *
	 * @return void
	 */
	public function test_to_entity(): void
	{
		$dto = new UserDatabase([
			'id' => 1,
			'name' => 'John Doe',
			'email' => 'john@example.com',
			'password' => password_hash('secret123', PASSWORD_BCRYPT),
			'is_active' => 1,
			'role' => 'admin',
			'created_at' => '2026-09-01 10:00:00',
			'updated_at' => '2026-09-02 11:00:00',
			'deleted_at' => null,
		]);

		$user = UserMapper::to_entity($dto);

		$this->assertInstanceOf(User::class, $user);
		$this->assertEquals(1, $user->get_id());
		$this->assertEquals('John Doe', $user->get_name());
		$this->assertEquals('john@example.com', (string) $user->get_email());
		$this->assertTrue($user->is_active());
		$this->assertEquals('admin', $user->get_role());
		$this->assertFalse($user->is_deleted());
	}

	/**
	 * Test mapping multiple raw database rows to User entities.
	 *
	 * @return void
	 */
	public function test_to_entities(): void
	{
		$rows = [
			[
				'id' => 1,
				'name' => 'Alice',
				'email' => 'alice@example.com',
				'password' => password_hash('secret1', PASSWORD_BCRYPT),
				'is_active' => 1,
				'role' => 'student',
			],
			[
				'id' => 2,
				'name' => 'Bob',
				'email' => 'bob@example.com',
				'password' => password_hash('secret2', PASSWORD_BCRYPT),
				'is_active' => 0,
				'role' => 'admin',
			],
		];

		$entities = UserMapper::to_entities($rows);

		$this->assertCount(2, $entities);
		$this->assertEquals('Alice', $entities[0]->get_name());
		$this->assertEquals('Bob', $entities[1]->get_name());
		$this->assertEmpty(UserMapper::to_entities([]));
	}

	/**
	 * Test mapping User entity to database array for creation.
	 *
	 * @return void
	 */
	public function test_to_database_create(): void
	{
		$user = User::create('Carlos', new Email('carlos@example.com'), 'pass123');
		$data = UserMapper::to_database_create($user);

		$this->assertEquals('Carlos', $data['name']);
		$this->assertEquals('carlos@example.com', $data['email']);
		$this->assertEquals(1, $data['is_active']);
		$this->assertNotEmpty($data['password']);
	}

	/**
	 * Test mapping User entity to database array for update.
	 *
	 * @return void
	 */
	public function test_to_database_update(): void
	{
		$user = User::create('Carlos Updated', new Email('carlos2@example.com'), 'pass456', 5);
		$data = UserMapper::to_database_update($user);

		$this->assertEquals('Carlos Updated', $data['name']);
		$this->assertEquals('carlos2@example.com', $data['email']);
		$this->assertEquals(1, $data['is_active']);
		$this->assertNull($data['deleted_at']);
	}
}
