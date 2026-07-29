---
name: ci3-domain
description: Use when creating domain entities, value objects, domain exceptions, or repository interfaces for CodeIgniter 3 projects following DDD-lite architecture.
---

# CI3 Domain Layer

## When to Use

Use this skill when creating:
- Entity classes (e.g., User.php, Course.php)
- Value Objects (e.g., Email.php, Role.php)
- Domain Exception classes (e.g., AppException.php, NotFoundException.php)
- Repository Interfaces (e.g., UserRepositoryInterface.php)

## Location

Domain files go in `application/domain/<BoundedContext>/` or `application/domain/exceptions/`

Example:
```
application/domain/identity/User.php
application/domain/identity/Email.php
application/domain/exceptions/AppException.php
application/domain/exceptions/NotFoundException.php
application/domain/exceptions/ValidationException.php
application/domain/exceptions/ConflictException.php
```

## Namespace

Domain classes use `app\domain\<BoundedContext>` or `app\domain\exceptions` namespace (PSR-4).

```php
namespace app\domain\identity;
namespace app\domain\exceptions;
```

## Domain Exception Pattern

All custom exceptions inherit from `AppException`:

```php
<?php

namespace app\domain\exceptions;

class AppException extends \DomainException
{
    protected int $statusCode;
    protected array $errors;

    public function __construct(string $message = "", int $statusCode = 400, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

Derived semantic exceptions:
- `NotFoundException`: status 404
- `ValidationException`: status 422
- `ConflictException`: status 409
- `UnauthorizedException`: status 401
- `ForbiddenException`: status 403

## Entity Pattern

```php
<?php

namespace app\domain\identity;

/**
 * Entity que representa um usuário do sistema.
 */
class User
{
    private $id;
    private $name;
    private $email;  // Value Object

    /**
     * Cria um novo usuário.
     *
     * @param string $name Nome do usuário
     * @param Email $email Email do usuário (Value Object)
     * @param string $password Senha em texto plano
     * @return self
     */
    public static function create(string $name, Email $email, string $password): self
    {
        $user = new self();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->created_at = new \DateTime();
        return $user;
    }

    /**
     * Hidrata um usuário a partir de um registro do banco.
     *
     * @param array $row Registro do banco de dados
     * @return self
     */
    public static function from_database(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->name = $row['name'];
        $user->email = new Email($row['email']);
        return $user;
    }

    /**
     * Obtém o ID do usuário.
     *
     * @return int|null
     */
    public function get_id(): ?int
    {
        return $this->id;
    }
}
```

## Value Object Pattern

```php
<?php

namespace app\domain\identity;

use app\domain\exceptions\ValidationException;

/**
 * Value Object que representa um endereço de email.
 */
class Email
{
    private $value;

    public function __construct(string $email)
    {
        $trimmed = trim($email);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Email inválido: {$email}");
        }
        $this->value = strtolower($trimmed);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === (string) $other;
    }
}
```

## Repository Interface Pattern

```php
<?php

namespace app\domain\identity;

interface UserRepositoryInterface
{
    public function find_by_id(int $id): ?User;

    public function find_by_email(Email $email): ?User;

    public function save(User $user): void;

    public function delete(int $id): void;
}
```

## Rules

1. Domain classes MUST NOT depend on CI3 (no `get_instance()`, no `CI_Model`)
2. Domain exceptions inherit from `app\domain\exceptions\AppException`
3. Use `private` properties with getters (no setters for immutable fields)
4. Factory methods: `create()` for new entities, `from_database()` for hydration
5. Value Objects must be immutable and implement `__toString()`
6. Repository Interfaces define contracts, NOT implementations
7. All classes and methods MUST have docblocks with `@param` and `@return`
8. Opening braces `{` on the NEXT line for classes and methods (PSR-12)
9. Use PSR-4 namespaces: `app\domain\<BoundedContext>\`
10. Directories are lowercase: `domain/`, `identity/`, `exceptions/`
11. Files are PascalCase: `User.php`, `Email.php`, `AppException.php`
