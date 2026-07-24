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
        // ...
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
            throw new \InvalidArgumentException("Email inválido: {$email}");
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
3. Factory methods: `create()` for new entities, `from_database()` for hydration
4. Value Objects must be immutable and implement `__toString()`
5. Repository Interfaces define contracts, NOT implementations
6. All classes and methods MUST have docblocks with `@param` and `@return`
7. Opening braces `{` on the NEXT line for classes and methods (PSR-12)
8. Use PSR-4 namespaces: `app\domain\<BoundedContext>\`
9. Directories are lowercase: `domain/`, `identity/`
10. Files are PascalCase: `User.php`, `Email.php`
