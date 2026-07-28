---
name: ci3-domain
description: Use when creating domain entities, value objects, or repository interfaces for CodeIgniter 3 projects following DDD-lite architecture.
---

# CI3 Domain Layer

## When to Use

Use this skill when creating:
- Entity classes (e.g., User.php, Course.php)
- Value Objects (e.g., Email.php, Role.php)
- Repository Interfaces (e.g., UserRepositoryInterface.php)

## Location

Domain files go in `application/domain/<BoundedContext>/`

Example:
```
application/domain/identity/User.php
application/domain/identity/Email.php
application/domain/identity/UserRepositoryInterface.php
```

## Namespace

All domain classes use the `app\domain\identity` namespace (PSR-4).

```php
namespace app\domain\identity;
```

## Entity Pattern

```php
<?php

namespace app\domain\identity;

use DateTime;

/**
 * Entity representing a system user.
 */
class User
{
    private ?int $id = null;
    private string $name;
    private Email $email;
    private string $password;
    private bool $is_active = true;
    private array $role_ids = [];
    private ?string $role = null;
    private ?DateTime $created_at = null;
    private ?DateTime $updated_at = null;
    private ?DateTime $deleted_at = null;

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
        return $user;
    }

    /**
     * Hydrate a user from a database record.
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
        // ...
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

> **Note:** Entity `create()` methods MUST NOT set `created_at` or `updated_at`. Timestamps are managed by database triggers.

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
2. Use `private` properties with getters (no setters for immutable fields)
3. **All properties MUST use PHP 8.2 strict types** (e.g., `private int $id`, `private ?string $role = null`). Nullable properties that may be uninitialized must have `= null` default.
4. Factory methods: `create()` for new entities, `from_database()` for hydration
5. Value Objects must be immutable and implement `__toString()`
6. Repository Interfaces define contracts, NOT implementations
7. All classes and methods MUST have docblocks with `@param` and `@return` — **always in English**
8. Opening braces `{` on the NEXT line for classes and methods (PSR-12)
9. Use PSR-4 namespaces: `app\domain\<BoundedContext>\`
10. Directories are lowercase: `domain/`, `identity/`
11. Files are PascalCase: `User.php`, `Email.php`
12. **Entity `create()` methods MUST NOT set `created_at` or `updated_at`** — timestamps are managed by database triggers.
13. Typed properties that are nullable and not set in `create()` MUST default to `null` (e.g., `private ?int $id = null`).
