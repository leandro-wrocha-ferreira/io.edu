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
 * Caso de uso para autenticar um usuário no sistema.
 */
class AuthenticateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Construtor.
     *
     * @param \app\domain\identity\UserRepositoryInterface|null $repository Repository para testes (opcional)
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
     * Executa a autenticação.
     *
     * @param string $email Email do usuário
     * @param string $password Senha em texto plano
     * @return User Usuário autenticado
     * @throws UnauthorizedException Quando credenciais são inválidas
     * @throws ForbiddenException Quando conta estiver desativada
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

## Model Factory Pattern

Use cases load models via `Model_factory` instead of `get_instance()`:

```php
use app\factories\Model_factory;

// Inside use case constructor:
$this->user_repository = Model_factory::make('user_model');
```

## Rules

1. Use Cases use `Model_factory` to load CI3 models (not `get_instance()`)
2. Accept repository in constructor for testability (dependency injection)
3. Use Cases orchestrate domain logic, NOT implement it
4. One Use Case per business action (Single Responsibility)
5. Method name: `execute()` (consistent across all use cases)
6. **Throw Semantic Domain Exceptions**: Use specific exception classes from `app\domain\exceptions\` (`NotFoundException`, `ValidationException`, `ConflictException`, `UnauthorizedException`, `ForbiddenException`). Never throw generic `\RuntimeException` or return `false`/`null` for errors.
7. Return entities, not arrays or raw objects
8. All classes and methods MUST have docblocks with `@param` and `@return`
9. Opening braces `{` on the NEXT line for classes and methods (PSR-12)
10. Directories are lowercase: `usecases/`, `identity/`, `admin/`
11. Files are PascalCase: `AuthenticateUserUseCase.php`, `GetUserUseCase.php`
