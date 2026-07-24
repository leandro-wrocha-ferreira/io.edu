<?php

use app\domain\identity\Email;
use app\domain\identity\User;

class UserTest extends \PHPUnit\Framework\TestCase
{
    public function test_create_user()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'password123');

        $this->assertEquals('John', $user->get_name());
        $this->assertEquals('john@example.com', (string) $user->get_email());
        $this->assertTrue($user->verify_password('password123'));
        $this->assertNull($user->get_id());
    }

    public function test_user_verify_correct_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'secret123');

        $this->assertTrue($user->verify_password('secret123'));
    }

    public function test_user_verify_wrong_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'secret123');

        $this->assertFalse($user->verify_password('wrongpassword'));
    }

    public function test_user_change_password()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'oldpass');

        $user->change_password('newpass');

        $this->assertTrue($user->verify_password('newpass'));
        $this->assertFalse($user->verify_password('oldpass'));
    }

    public function test_user_from_database()
    {
        $row = [
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertEquals(1, $user->get_id());
        $this->assertEquals('John', $user->get_name());
        $this->assertEquals('john@example.com', (string) $user->get_email());
        $this->assertEquals('student', $user->get_role());
    }

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
    }

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

    public function test_user_is_deleted()
    {
        $row = [
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => '2026-01-02 00:00:00',
        ];

        $user = User::from_database($row);

        $this->assertTrue($user->is_deleted());
    }

    public function test_user_not_deleted()
    {
        $row = [
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertFalse($user->is_deleted());
    }
}
