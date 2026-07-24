<?php

use app\domain\identity\Email;
use app\domain\identity\User;

class User_repositoryTest extends \PHPUnit\Framework\TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'student',
                created_at DATETIME,
                updated_at DATETIME,
                deleted_at DATETIME
            )
        ");
    }

    public function test_insert_and_find_user()
    {
        $email = new Email('test@example.com');
        $user = User::create('Test User', $email, 'password123');

        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role, created_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user->get_name(),
            (string) $user->get_email(),
            $user->get_password(),
            'student',
            $user->get_created_at()->format('Y-m-d H:i:s'),
        ]);

        $inserted_id = $this->pdo->lastInsertId();

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$inserted_id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotNull($row);
        $this->assertEquals('Test User', $row['name']);
        $this->assertEquals('test@example.com', $row['email']);
    }

    public function test_find_by_email()
    {
        $this->pdo->exec("
            INSERT INTO users (name, email, password, role, created_at)
            VALUES ('Test', 'test@example.com', '" . password_hash('pass', PASSWORD_BCRYPT) . "', 'student', '2026-01-01 00:00:00')
        ");

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['test@example.com']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotNull($row);
        $this->assertEquals('Test', $row['name']);
    }

    public function test_find_by_email_returns_null_for_nonexistent()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['notfound@example.com']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertFalse($row);
    }

    public function test_user_entity_from_database_row()
    {
        $row = [
            'id' => 1,
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => password_hash('pass', PASSWORD_BCRYPT),
            'role' => 'student',
            'created_at' => '2026-01-01 00:00:00',
            'updated_at' => null,
            'deleted_at' => null,
        ];

        $user = User::from_database($row);

        $this->assertEquals(1, $user->get_id());
        $this->assertEquals('Test', $user->get_name());
        $this->assertTrue($user->verify_password('pass'));
    }
}
