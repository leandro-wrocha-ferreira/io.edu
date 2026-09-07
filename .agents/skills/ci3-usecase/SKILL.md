---
name: ci3-usecase
description: Use when creating use cases (application layer) for CodeIgniter 3 projects following DDD-lite architecture.
---

# CI3 Use Case Layer

## When to Use

Use this skill when creating:
- Use Case classes (e.g., CreateUserUseCase.php, AuthenticateUserUseCase.php, GetUserUseCase.php)
- Application services that orchestrate domain logic

## Location

Use case files go in `application/usecases/<BoundedContext>/`

Example:
```
application/usecases/identity/CreateUserUseCase.php
application/usecases/identity/AuthenticateUserUseCase.php
application/usecases/admin/GetUserUseCase.php
```

## Namespace

All use case classes use the `app\usecases\<BoundedContext>` namespace (PSR-4).

```php
namespace app\usecases\identity;
namespace app\usecases\admin;
```

## Use Case Pattern

```php
<?php

namespace app\usecases\identity;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\exceptions\UnauthorizedException;
use app\domain\exceptions\ForbiddenException;
use app\factories\Model_factory;

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
            $this->user_repository = Model_factory::make('user_model');
        }
    }

    /**
     * Execute user authentication.
     *
     * @param string $email User email
     * @param string $password Plain text password
     * @return User Authenticated user entity
     * @throws UnauthorizedException When credentials are invalid
     * @throws ForbiddenException When account is deactivated
     */
    public function execute(string $email, string $password): User
    {
        $user = $this->user_repository->find_by_email(new Email($email));

        if ($user === null) {
            throw new UnauthorizedException("Credenciais inválidas");
        }

        if ($user->is_deleted()) {
            throw new ForbiddenException("Conta desativada");
        }

        if (!$user->verify_password($password)) {
            throw new UnauthorizedException("Credenciais inválidas");
        }

        return $user;
    }
}
```

## Composition & Model Factory Pattern

The logical dependency of a Use Case is **always** the Domain Repository Interface (`UserRepositoryInterface`). 

`Model_factory` is an infrastructure composition helper used solely as a default constructor fallback when no repository implementation is explicitly injected:

```php
use app\domain\identity\UserRepositoryInterface;
use app\factories\Model_factory;

// Inside use case constructor:
public function __construct(?UserRepositoryInterface $repository = null)
{
    $this->user_repository = $repository ?? Model_factory::make('user_model');
}
```

> [!IMPORTANT]
> **Boundary Rule**: `Model_factory` belongs to the Infrastructure/Factory layer (`app\factories\`). It MUST NEVER be referenced or used inside the Domain layer (`app\domain\`).

## Rules

1. **Logical Dependency**: Use Cases depend on Domain Repository Interfaces, accepting them in the constructor for testability.
2. **Infrastructure Fallback**: Use `Model_factory::make('model_name')` solely for constructor default assignment.
3. **Orchestration Only**: Use Cases orchestrate business rules and domain operations; they do NOT contain UI logic or direct SQL.
4. **Single Responsibility**: One Use Case per business action with a standard `execute()` method.
5. **Throw Semantic Domain Exceptions**: Always throw semantic exceptions from `app\domain\exceptions\` (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`). Never throw generic `\RuntimeException`.
6. **Return Domain Entities**: Return hydrated Domain Entities or DTOs, not raw database associative arrays.
7. **All Docblocks in English**: Classes, methods, `@param`, `@return`, and `@throws` MUST be in English.
8. **PSR-12**: Opening braces `{` on the next line for classes and methods.
9. **Namespaces & Paths**: Directory lowercase (`usecases/<context>/`), file PascalCase (`AuthenticateUserUseCase.php`).

---

## Anti-Patterns

❌ **Throwing `\RuntimeException`**: Throwing generic unclassified exceptions instead of semantic exceptions from `app\domain\exceptions\`.
❌ **Leaking `Model_factory` into Domain**: Referencing or calling `Model_factory` inside Entities or Value Objects.
❌ **Direct `get_instance()` Coupling**: Calling `$CI =& get_instance()` directly inside Use Cases instead of using constructor dependency injection with `Model_factory` fallback.
❌ **Returning Raw Arrays**: Returning raw database rows or untyped associative arrays from `execute()` when representing domain models.
❌ **Multiple Business Actions in One Use Case**: Creating a monolithic service with multiple unrelated public methods instead of dedicated single-action Use Cases.

