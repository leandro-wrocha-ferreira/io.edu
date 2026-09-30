<?php

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\domain\identity\value_objects\Password;

/**
 * Test suite for User entity.
 *
 * Covers creation with and without ID, password verification via Password VO,
 * activation state lifecycle, soft delete lifecycle, and setters.
 */
class UserTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test creating a user without ID.
	 *
	 * @return void
	 */
	public function test_create_user_without_id(): void
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'password123');

		$this->assertEquals('John', $user->get_name());
		$this->assertEquals('john@example.com', (string) $user->get_email());
		$this->assertTrue($user->get_password()->verify('password123'));
		$this->assertNull($user->get_id());
		$this->assertTrue($user->is_active());
		$this->assertNotNull($user->get_created_at());
		$this->assertFalse($user->is_deleted());
	}

	/**
	 * Test creating a user with ID and custom attributes.
	 *
	 * @return void
	 */
	public function test_create_user_with_id(): void
	{
		$email = new Email('jane@example.com');
		$user = User::create('Jane', $email, 'secret456', 42, false);

		$this->assertEquals(42, $user->get_id());
		$this->assertEquals('Jane', $user->get_name());
		$this->assertFalse($user->is_active());
		$this->assertTrue($user->get_password()->verify('secret456'));
	}

	/**
	 * Test password verification via Password VO.
	 *
	 * @return void
	 */
	public function test_password_verification(): void
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'secret123');

		$this->assertTrue($user->get_password()->verify('secret123'));
		$this->assertFalse($user->get_password()->verify('wrongpassword'));
	}

	/**
	 * Test changing the user password.
	 *
	 * @return void
	 */
	public function test_user_change_password(): void
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'oldpass');

		$user->change_password('newpass');

		$this->assertTrue($user->get_password()->verify('newpass'));
		$this->assertFalse($user->get_password()->verify('oldpass'));
	}

	/**
	 * Test user activation and inactivation lifecycle.
	 *
	 * @return void
	 */
	public function test_user_activation_lifecycle(): void
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'pass123', null, true);

		$this->assertTrue($user->is_active());

		$user->inactivate();
		$this->assertFalse($user->is_active());

		$user->activate();
		$this->assertTrue($user->is_active());
	}

	/**
	 * Test soft delete and restore.
	 *
	 * @return void
	 */
	public function test_user_delete_and_restore(): void
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, '123');
		$this->assertFalse($user->is_deleted());

		$user->delete();
		$this->assertTrue($user->is_deleted());
		$this->assertNotNull($user->get_deleted_at());

		$user->restore();
		$this->assertFalse($user->is_deleted());
		$this->assertNull($user->get_deleted_at());
	}

	/**
	 * Test setting name and email.
	 *
	 * @return void
	 */
	public function test_user_setters(): void
	{
		$email = new Email('orig@example.com');
		$user = User::create('Original', $email, '123');

		$user->set_name('Novo Nome');
		$new_email = new Email('novo@example.com');
		$user->set_email($new_email);

		$this->assertEquals('Novo Nome', $user->get_name());
		$this->assertEquals('novo@example.com', (string) $user->get_email());
	}

	/**
	 * Test role getter and setter.
	 *
	 * @return void
	 */
	public function test_user_role(): void
	{
		$email = new Email('role@example.com');
		$user = User::create('Role User', $email, '123', null, true, null, null, null, 'admin');

		$this->assertEquals('admin', $user->get_role());

		$user->set_role('student');
		$this->assertEquals('student', $user->get_role());
	}
}
