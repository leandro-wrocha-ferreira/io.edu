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

namespace app\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

/**
 * Use case for activating a user.
 */
class ActivateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface $user_repository
     */
    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * Activate the user.
     *
     * @param int $user_id User ID
     * @return User
     * @throws NotFoundException When user not found
     */
    public function execute(int $user_id): User
    {
        $user = $this->user_repository->find_by_id($user_id);
        if ($user === null) {
            throw new NotFoundException("User not found");
        }

        $user->set_active(true);
        return $this->user_repository->save($user);
    }
}
```

## Rules

1. **Logical Dependency**: Use Cases depend on Domain Repository Interfaces via STRICT constructor dependency injection. (No more optional parameters or Model_factory fallbacks inside the Use Case itself).
2. **Orchestration Only**: Use Cases orchestrate business rules and domain operations; they do NOT contain UI logic or direct SQL.
3. **Single Responsibility**: One Use Case per business action with a standard `execute()` method.
4. **Throw Semantic Domain Exceptions**: Always throw semantic exceptions from `app\domain\exceptions\` (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`). Never throw generic `\RuntimeException`.
5. **Return Domain Entities**: Return hydrated Domain Entities or DTOs, not raw database associative arrays.
6. **Global Style**: All style rules (PSR-12, docblocks, no single-letter variables) MUST follow the global conventions defined in `GEMINI.md`.

---

## Anti-Patterns

❌ **Throwing `\RuntimeException`**: Throwing generic unclassified exceptions instead of semantic exceptions from `app\domain\exceptions\`.
❌ **Direct `get_instance()` Coupling**: Calling `$CI =& get_instance()` directly inside Use Cases instead of using constructor dependency injection.
❌ **Using Model_factory internally**: Instantiating repositories inside the Use Case constructor with `Model_factory::make()`. Dependency must be injected from the outside.
❌ **Returning Raw Arrays**: Returning raw database rows or untyped associative arrays from `execute()` when representing domain models.
❌ **Multiple Business Actions in One Use Case**: Creating a monolithic service with multiple unrelated public methods instead of dedicated single-action Use Cases.

