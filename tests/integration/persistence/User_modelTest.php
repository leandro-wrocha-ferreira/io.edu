<?php

use app\domain\identity\Email;
use app\domain\identity\User;

/**
 * Integration test for User persistence layer.
 *
 * Uses SQLite in-memory to test CRUD operations
 * and entity hydration from database rows.
 */
class User_repositoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var \PDO */
    private $pdo;

    /**
     * Set up in-memory SQLite database and create users table.
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SQLite PDO driver is not available in this environment.');
            return;
        }

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

    /**
     * Test inserting a user and retrieving it by ID.
     *
     * @return void
     */
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

    /**
     * Test finding a user by email address.
     *
     * @return void
     */
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

    /**
     * Test that searching for a non-existing email returns false.
     *
     * @return void
     */
    public function test_find_by_email_returns_null_for_nonexistent()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['notfound@example.com']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertFalse($row);
    }

    /**
     * Test hydrating a User entity from a raw database row.
     *
     * @return void
     */
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
