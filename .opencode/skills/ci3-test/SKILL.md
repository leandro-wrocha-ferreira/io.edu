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

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\Email;
use app\domain\identity\User;
use app\usecases\admin\ActivateUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

class ActivateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
    private $mock_user_repository;

    protected function setUp(): void
    {
        $this->mock_user_repository = new MockUserRepository();
    }

    public function test_activate_user_success()
    {
        $email = new Email('inactive@example.com');
        $user = User::create('Inactive User', $email, 'password123');
        $user->set_active(false);
        
        $user = $this->mock_user_repository->save($user);
        $use_case = new ActivateUserUseCase($this->mock_user_repository);
        $user = $use_case->execute($user->get_id());
        
        $this->assertTrue($user->is_active());
    }
}
```

## Running Tests

```bash
# Unit tests only
docker compose exec app vendor/bin/phpunit tests/unit/

# All PHPUnit tests with coverage
docker compose exec app composer test:coverage
```

## File Structure

```
tests/
├── bootstrap.php
├── unit/
│   ├── domain/
│   │   └── identity/
│   │       ├── UserTest.php
│   │       └── EmailTest.php
|   ├── mocks/
|   |   └── repositories/
|   |       └── MockUserRepository.php
│   └── usecases/
│       └── admin/
│           └── ActivateUserUseCaseTest.php
```

## Rules

1. Unit tests: NO database, NO HTTP.
2. Repositories must be mocked and centralized in `tests/unit/mocks/repositories/`. Mocks must implement the exact methods defined in the interface, no more, no less.
3. One test class per source class. Every UseCase must have its own 1:1 dedicated test file.
4. Test both success and failure scenarios.
5. Use descriptive test method names: `test_activate_user_not_found_throws_exception`.
6. Use lowercase namespaces matching the directory structure: `tests\unit\usecases\admin`, `tests\unit\domain\identity`.
7. **CRITICAL:** Tests use `classmap` autoloading. You MUST run `docker compose exec app composer dump-autoload` whenever a new test or mock file is created, moved, or renamed.
