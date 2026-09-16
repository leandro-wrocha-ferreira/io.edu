<?php

use app\domain\identity\Email;
use app\domain\identity\User;

/**
 * Test suite for User entity.
 *
 * Covers creation, password verification, role checks,
 * database hydration, getters, setters, and soft delete lifecycle.
 */
class UserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test creating a user with valid data.
     *
     * @return void
     */
    public function test_create_user()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'password123');

        $this->assertEquals('John', $user->get_name());
        $this->assertEquals('john@example.com', (string) $user->get_email());
        $this->assertTrue($user->verify_password('password123'));
        $this->assertNull($user->get_id());
        $this->assertTrue($user->is_active());
        $this->assertNotNull($user->get_created_at());
    }

    /**
     * Test password verification with the correct password.
     *
     * @return void
     */
    public function test_user_verify_correct_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'secret123');

        $this->assertTrue($user->verify_password('secret123'));
    }

    /**
     * Test password verification with a wrong password.
     *
     * @return void
     */
    public function test_user_verify_wrong_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'secret123');

        $this->assertFalse($user->verify_password('wrongpassword'));
    }

    /**
     * Test changing the user password.
     *
     * @return void
     */
    public function test_user_change_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'oldpass');

        $user->change_password('newpass');

        $this->assertTrue($user->verify_password('newpass'));
        $this->assertFalse($user->verify_password('oldpass'));
    }

    /**
     * Test hydrating a User from a database row.
     *
     * @return void
     */
    public function test_user_from_database()
    {
        $row = [
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'role_ids' => '[1,2]',
            'is_active' => 1,
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertEquals(1, $user->get_id());
        $this->assertEquals('John', $user->get_name());
        $this->assertEquals('john@example.com', (string) $user->get_email());
        $this->assertEquals('student', $user->get_role());
        $this->assertEquals([1, 2], $user->get_role_ids());
        $this->assertTrue($user->is_active());
        $this->assertNotNull($user->get_password());
    }

    /**
     * Test role check for admin users.
     *
     * @return void
     */
    public function test_user_is_admin()
    {
        $row = [
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'admin',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertTrue($user->is_admin());
        $this->assertFalse($user->is_student());
        $this->assertFalse($user->is_admin_master());
        $this->assertTrue($user->has_role('admin'));
    }

    /**
     * Test role check for student users.
     *
     * @return void
     */
    public function test_user_is_student()
    {
        $row = [
            'id' => 1,
            'name' => 'Student',
            'email' => 'student@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertTrue($user->is_student());
        $this->assertFalse($user->is_admin());
    }

    /**
     * Test admin master check.
     *
     * @return void
     */
    public function test_user_is_admin_master()
    {
        $row = [
            'id' => 1,
            'name' => 'Master',
            'email' => 'master@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'admin-master',
        ];

        $user = User::from_database($row);
        $this->assertTrue($user->is_admin_master());
    }

    /**
     * Test soft delete and restore.
     *
     * @return void
     */
    public function test_user_delete_and_restore()
    {
        $user = User::create('John', new Email('john@test.com'), '123');
        $this->assertFalse($user->is_deleted());

        $user->delete();
        $this->assertTrue($user->is_deleted());

        $user->restore();
        $this->assertFalse($user->is_deleted());
    }

    /**
     * Test setters and getters.
     *
     * @return void
     */
    public function test_user_setters()
    {
        $user = User::create('Original', new Email('orig@test.com'), '123');
        $user->set_id(42);
        $user->set_name('Novo Nome');
        $user->set_email(new Email('novo@test.com'));
        $user->set_active(false);
        $user->set_role('instructor');
        $user->set_role_ids([4, 5]);

        $this->assertEquals(42, $user->get_id());
        $this->assertEquals('Novo Nome', $user->get_name());
        $this->assertEquals('novo@test.com', (string) $user->get_email());
        $this->assertFalse($user->is_active());
        $this->assertEquals('instructor', $user->get_role());
        $this->assertEquals([4, 5], $user->get_role_ids());
    }
}
