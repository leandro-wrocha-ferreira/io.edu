---
name: ci3-usecase
description: Use when creating use cases (application layer) for CodeIgniter 3 projects following DDD-lite architecture.
---

# CI3 Use Case Layer

## When to Use

Use this skill when creating:
- Use Case classes (e.g., CreateUserUseCase.php, AuthenticateUserUseCase.php)
- Application services that orchestrate domain logic

## Location

Use case files go in `application/usecases/<BoundedContext>/`

Example:
```
application/usecases/identity/CreateUserUseCase.php
application/usecases/identity/AuthenticateUserUseCase.php
```

## Namespace

All use case classes use the `app\usecases\identity` namespace (PSR-4).

```php
namespace app\usecases\identity;
```

## Use Case Pattern

```php
<?php

namespace app\usecases\identity;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\Factories\Model_factory;

/**
 * Use case for authenticating a user in the system.
 */
class AuthenticateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface|null $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->user_repository = $repository;
        } else {
            $this->user_repository = Model_factory::make('User_model');
        }
    }

    /**
     * Execute authentication.
     *
     * @param string $email User email
     * @param string $password Plain text password
     * @return User Authenticated user
     * @throws \RuntimeException When invalid credentials or deactivated account
     */
    public function execute(string $email, string $password): User
    {
        $user = $this->user_repository->find_by_email(new Email($email));

        if ($user === null) {
            throw new \RuntimeException("Invalid credentials");
        }

        if ($user->is_deleted()) {
            throw new \RuntimeException("Deactivated account");
        }

        if (!$user->verify_password($password)) {
            throw new \RuntimeException("Invalid credentials");
        }

        return $user;
    }
}
```

## Model Factory Pattern

Use cases load models via `Model_factory` instead of `get_instance()`:

```php
use app\Factories\Model_factory;

// Inside use case constructor:
$this->user_repository = Model_factory::make('User_model');
```

## Rules

1. Use Cases use `Model_factory` to load CI3 models (not `get_instance()`)
2. Accept repository in constructor for testability (dependency injection)
3. Use Cases orchestrate domain logic, NOT implement it
4. One Use Case per business action (Single Responsibility)
5. Method name: `execute()` (consistent across all use cases)
6. Throw exceptions for business rule violations (never return false/null for errors)
7. Return entities, not arrays or objects
8. All classes and methods MUST have docblocks with `@param` and `@return` — **always in English**
9. Opening braces `{` on the NEXT line for classes and methods (PSR-12)
10. Directories are lowercase: `usecases/`, `identity/`
11. Files are PascalCase: `AuthenticateUserUseCase.php`
