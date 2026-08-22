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

use Application\Domain\Identity\Email;
use Application\Domain\Identity\User;

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

Test models with real database (SQLite). Verify queries and persistence.

```php
<?php

use Application\Domain\Identity\Email;
use Application\Domain\Identity\User;

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

    public function test_find_by_email_returns_user()
    {
        // Insert test data
        $this->pdo->exec("INSERT INTO users (name, email, password, role, created_at) VALUES ('Test', 'test@example.com', '" . password_hash('pass', PASSWORD_BCRYPT) . "', 'student', '2026-01-01 00:00:00')");

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute(['test@example.com']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotNull($row);
        $this->assertEquals('Test', $row['name']);
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
        $I->amOnPage('/autenticacao/login');
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

1. Unit tests: NO database, NO HTTP, use mocks for repositories
2. Integration tests: use SQLite for database tests
3. E2E tests: use Codeception for browser tests (PHP native, no Playwright)
4. One test class per source class
5. Test both success and failure scenarios
6. Use descriptive test method names: `test_should_throw_exception_when_email_invalid`
7. Use PSR-4 namespaces for test classes: `Tests\Acceptance\`, `Tests\Unit\`, etc.
8. PHPUnit bootstrap loads Composer autoloader from `vendor/autoload.php`
