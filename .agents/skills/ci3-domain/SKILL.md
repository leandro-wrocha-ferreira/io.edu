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
 * Entity representing a system user.
 */
class User
{
    private $id;
    private $name;
    private $email;  // Value Object
    private ?\DateTime $created_at = null;

    /**
     * Create a new user.
     *
     * @param string $name User's name
     * @param Email $email User's email (Value Object)
     * @param string $password Plain text password
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
     * Hydrate a user from a database row.
     *
     * @param array $row Database record
     * @return self
     */
    public static function from_database(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->name = $row['name'];
        $user->email = new Email($row['email']);
        $user->created_at = isset($row['created_at']) ? new \DateTime($row['created_at']) : null;
        return $user;
    }

    /**
     * Get the user ID.
     *
     * @return int|null
     */
    public function get_id(): ?int
    {
        return $this->id;
    }
}
```

> [!NOTE]
> **In-Memory Timestamps vs Database Triggers**:
> `$user->created_at = new \DateTime()` in `create()` sets an in-memory timestamp so domain logic/getters can inspect it immediately. However, the database layer (`Model::save()`) **NEVER** includes `created_at` or `updated_at` in SQL insert/update arrays — those columns are managed exclusively by database triggers.

## Value Object Pattern

```php
<?php

namespace app\domain\identity;

/**
 * Value Object representing an email address.
 */
class Email
{
    private string $value;

    /**
     * Constructor.
     *
     * @param string $email Raw email address
     * @throws \InvalidArgumentException If email format is invalid
     */
    public function __construct(string $email)
    {
        $trimmed = trim($email);
        if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email: {$email}");
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

/**
 * Repository interface for User persistence.
 */
interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int|string $id User ID
     * @return User|null
     */
    public function find_by_id($id): ?User;

    /**
     * Find a user by email.
     *
     * @param Email $email User email
     * @return User|null
     */
    public function find_by_email(Email $email): ?User;

    /**
     * Save (insert or update) a user.
     *
     * @param User $user User entity to persist
     * @return void
     */
    public function save(User $user): void;

    /**
     * Delete users matching specified filter conditions.
     *
     * @param array $where Filter conditions (e.g. ['id' => $id])
     * @return bool
     */
    public function delete(array $where): bool;
}
```

## Rules

1. Domain classes MUST NOT depend on CI3 (no `get_instance()`, no `CI_Model`).
2. Domain exceptions inherit from `app\domain\exceptions\AppException`.
3. Value Object constructors throw `\InvalidArgumentException` for invalid scalar arguments.
4. Use `private` properties with getters (no setters for immutable fields).
5. Factory methods: `create()` for new entities, `from_database()` for hydration.
6. Value Objects must be immutable and implement `__toString()`.
7. Repository Interfaces define contracts matching `MY_Model` signatures (`find_by_id($id)`, `delete(array $where): bool`).
8. Models never persist `created_at` or `updated_at` (database triggers manage timestamps).
9. All classes and methods MUST have docblocks with `@param` and `@return` — **always in English**.
10. Opening braces `{` on the NEXT line for classes and methods (PSR-12).
11. Use PSR-4 namespaces: `app\domain\<BoundedContext>\`.
12. Directories are lowercase (`domain/`, `identity/`, `exceptions/`) and files are PascalCase (`User.php`, `Email.php`).

---

## Anti-Patterns

❌ **Framework Coupling in Domain**: Calling `get_instance()`, `CI_Model`, `CI_Controller`, or database helpers inside Domain classes.
❌ **Using `Model_factory` in Domain**: Domain entities and value objects must be pure PHP and must never instantiate models.
❌ **Mutable Value Objects**: Adding setters to Value Objects or modifying internal state after construction.
❌ **Typed `$id` in Repository Interfaces**: Declaring `find_by_id(int $id)` creates PHP 8.2 type incompatibility with `MY_Model::find_by_id($id)`. Keep `$id` untyped in parameter.
❌ **Legacy `delete(int $id)` signature**: Declaring `delete(int $id): void` in repository interfaces or models causes PHP 8.2 fatal compile error against `MY_Model::delete(array $where): bool`.
❌ **Portuguese Docblocks**: Writing `@param`, `@return`, or summaries in Portuguese instead of English.

