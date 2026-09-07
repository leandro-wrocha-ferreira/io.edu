---
name: ci3-test
description: Use when creating unit, integration, or E2E tests for CodeIgniter 3 projects.
---

# CI3 Testing Strategy

## Test Types

### Unit Tests (tests/unit/)

Test domain entities and use cases in isolation. No database, no HTTP.

```php
<?php

use app\domain\identity\Email;
use app\domain\identity\User;

class UserTest extends \PHPUnit\Framework\TestCase
{
    public function test_create_user_with_valid_data()
    {
        $email = new Email('test@example.com');
        $user = User::create('John', $email, 'password123');

        $this->assertEquals('John', $user->get_name());
        $this->assertEquals('test@example.com', (string) $user->get_email());
        $this->assertTrue($user->verify_password('password123'));
    }

    public function test_create_user_with_invalid_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid-email');
    }
}
```

### Integration Tests (tests/integration/)

Test persistence mapping, database schemas, and entity hydration using SQLite in-memory:

> [!NOTE]
> **CI3 & SQLite Test Strategy**:
> In CI3 DDD-lite, models extend `MY_Model` and depend on `$this->db`. Integration tests in `tests/integration/persistence/` test schema constraints and `from_database()` entity hydration using SQLite PDO. Direct model engine logic (CRUD execution, query compilation, audit logs) is verified in `tests/unit/MY_ModelTest.php` by mocking the query builder.

```php
<?php

use app\domain\identity\Email;
use app\domain\identity\User;

/**
 * Integration test for User persistence mapping and hydration.
 */
class User_modelTest extends \PHPUnit\Framework\TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SQLite PDO driver is not available.');
            return;
        }

        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Test schema mirroring the application users table (timestamps handled automatically)
        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'student',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT NULL,
                deleted_at DATETIME DEFAULT NULL
            )
        ");
    }

    public function test_insert_and_hydrate_user_entity(): void
    {
        $email = new Email('test@example.com');
        $user = User::create('Test User', $email, 'password123');

        // Note: Models never pass created_at/updated_at; DB defaults/triggers populate them
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $user->get_name(),
            (string) $user->get_email(),
            $user->get_password(),
            'student',
        ]);

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['test@example.com']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotNull($row);
        $hydrated = User::from_database($row);

        $this->assertEquals('Test User', $hydrated->get_name());
        $this->assertEquals('test@example.com', (string) $hydrated->get_email());
        $this->assertTrue($hydrated->verify_password('password123'));
    }
}
```

### E2E Tests (tests/acceptance/)

Test complete user flows with Codeception (PHP native).

```php
<?php

namespace Tests\Acceptance;

use Tests\AcceptanceTester;

class LoginCest
{
    public function loginPageLoadsCorrectly(AcceptanceTester $I)
    {
        $I->amOnPage('/entrar');
        $I->see('Login');
        $I->seeElement('#email');
        $I->seeElement('#password');
        $I->seeElement('button[type="submit"]');
    }
}
```

## Running Tests

```bash
# Unit tests only
vendor/bin/phpunit tests/unit/

# Integration tests only
vendor/bin/phpunit tests/integration/

# All PHPUnit tests with coverage
composer test:coverage

# E2E acceptance tests
vendor/bin/codecept run acceptance
```

## File Structure

```
tests/
├── bootstrap.php
├── codeception.yml
├── acceptance.suite.yml
├── unit/
│   ├── domain/
│   │   └── Identity/
│   │       ├── UserTest.php
│   │       └── EmailTest.php
│   └── usecases/
│       └── Identity/
│           └── AuthenticateUserUseCaseTest.php
├── integration/
│   └── persistence/
│       └── User_modelTest.php
├── acceptance/
│   ├── LoginCest.php
│   ├── DashboardCest.php
│   └── LogoutCest.php
└── _support/
    ├── AcceptanceTester.php
    └── Helper/
        └── Acceptance.php
```

## Rules

1. **Unit tests**: NO database, NO HTTP, use mocks/stubs for repositories.
2. **Integration tests**: Use SQLite in-memory to test schema persistence and entity hydration.
3. **Database Timestamps**: Models never persist `created_at` or `updated_at`. Test schemas must rely on `DEFAULT CURRENT_TIMESTAMP` or leave timestamps to triggers.
4. **E2E tests**: Use Codeception for browser tests against actual application routes (e.g. `/entrar`).
5. **One test class per source class**.
6. **Test both success and failure scenarios**.
7. **Use descriptive test method names**: `test_create_user_with_invalid_email_throws_exception`.
8. **Use PSR-4 namespaces**: `app\domain\...` for domain classes and `Tests\Acceptance\` for Codeception.
9. **Coverage requirement**: Automated tests MUST cover at least **80%** of application code.

---

## Anti-Patterns

❌ **Uppercase `Application\` in Namespaces**: Writing `use Application\Domain\...` instead of `use app\domain\...` (breaks PSR-4 composer autoloading).
❌ **Raw SQL Assertions Without Hydration**: Testing persistence without verifying Domain Entity hydration (`from_database()`).
❌ **Manual Timestamps in Test Inserts**: Manually passing `created_at` in insert queries as if models set them. Models NEVER persist timestamps.
❌ **Stale Routes in Acceptance Tests**: Using legacy routes like `/autenticacao/login` instead of the configured route `/entrar`.
❌ **Real Database Calls in Unit Tests**: Querying SQLite or MySQL in `tests/unit/` (mock repositories instead).

