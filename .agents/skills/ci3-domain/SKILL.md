---
name: ci3-domain
description: Use when creating domain entities, value objects, domain exceptions, or repository interfaces for CodeIgniter 3 projects following DDD-lite architecture.
---

# CI3 Domain Layer

## When to Use

Use this skill when creating:
- Entity classes (e.g., User.php, Course.php)
- Value Objects (e.g., Email.php, Password.php)
- Domain Constants (e.g., RoleSlug.php, PermissionSlug.php)
- Domain Exception classes (e.g., AppException.php, NotFoundException.php)
- Repository Interfaces (e.g., UserRepositoryInterface.php)

## Location & Directory Structure

Domain files go in `application/domain/<BoundedContext>/` using dedicated subdirectories for Value Objects, Constants, and Repositories, or `application/domain/exceptions/`:

Example:
```
application/domain/identity/User.php
application/domain/identity/constants/RoleSlug.php
application/domain/identity/value_objects/Email.php
application/domain/identity/value_objects/Password.php
application/domain/identity/repositories/UserRepositoryInterface.php
application/domain/authorization/constants/PermissionSlug.php
application/domain/exceptions/AppException.php
application/domain/exceptions/NotFoundException.php
application/domain/exceptions/ValidationException.php
application/domain/exceptions/ConflictException.php
```

## Namespace

Domain classes use PSR-4 namespaces matching their subdirectory:

```php
namespace app\domain\<BoundedContext>;
namespace app\domain\<BoundedContext>\constants;
namespace app\domain\<BoundedContext>\value_objects;
namespace app\domain\<BoundedContext>\repositories;
namespace app\domain\exceptions;
```

Examples:
```php
namespace app\domain\identity;
namespace app\domain\identity\constants;
namespace app\domain\identity\value_objects;
namespace app\domain\identity\repositories;
namespace app\domain\authorization\constants;
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

Entities provide a **unified static `create()` method** that receives all properties necessary for the entity to exist, with default values for lifecycle and optional properties. Entities do **NOT** know about database rows (`from_database()`) and do **NOT** have separate `reconstitute()` methods:

```php
<?php

namespace app\domain\identity;

use app\domain\identity\value_objects\Email;
use app\domain\identity\value_objects\Password;
use DateTime;

/**
 * Entity representing a system user.
 */
final class User
{
	private ?int $id = null;
	private string $name;
	private Email $email;
	private Password $password;
	private bool $is_active = true;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;
	private ?DateTime $deleted_at = null;

	/**
	 * Create a user domain entity.
	 *
	 * @param string $name User's name
	 * @param Email $email User's email (Value Object)
	 * @param Password|string $password Plain text password or Password Value Object
	 * @param int|null $id User ID
	 * @param bool $is_active Active status
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 */
	public static function create(
		string $name,
		Email $email,
		Password|string $password,
		?int $id = null,
		bool $is_active = true,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$user = new self();
		$user->id = $id;
		$user->name = $name;
		$user->email = $email;
		$user->password = $password instanceof Password ? $password : new Password($password);
		$user->is_active = $is_active;
		$user->created_at = $created_at ?? new DateTime();
		$user->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$user->deleted_at = $deleted_at;

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
> `$user->created_at = $created_at ?? new DateTime()` sets an in-memory timestamp so domain logic/getters can inspect it immediately. However, the database layer (`Model::insert()`) **NEVER** includes `created_at` or `updated_at` in SQL insert/update arrays — those columns are managed exclusively by database triggers.

## Value Object Pattern

Value Objects are immutable and placed in `value_objects/`:

```php
<?php

namespace app\domain\identity\value_objects;

use InvalidArgumentException;

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
	 * @throws InvalidArgumentException If email format is invalid
	 */
	public function __construct(string $email)
	{
		$trimmed = trim($email);
		if (!filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException("Invalid email: {$email}");
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

## Domain Constants Pattern

Domain Constants define system-controlled identifiers, immutable role slugs, and permission keys governed by business rules. They eliminate magic strings and decouple business logic from unpredictable database autoincrement primary keys (e.g., avoiding hardcoded `id === 1` or `id === 3`).

### Location & Structure

- Placed in `application/domain/<BoundedContext>/constants/`.
- Declared as `final class` with `public const` members.
- Never instantiate domain constant classes.

### Example: Role Slugs

```php
<?php

namespace app\domain\identity\constants;

/**
 * Constants representing system role slugs.
 */
final class RoleSlug
{
	public const ADMIN = 'admin';
	public const STUDENT = 'student';

	public const ALL = [
		self::ADMIN,
		self::STUDENT,
	];

	/**
	 * Check if a given slug is a valid system role.
	 *
	 * @param string $slug Slug to validate
	 * @return bool
	 */
	public static function is_valid(string $slug): bool
	{
		return in_array($slug, self::ALL, true);
	}
}
```

### Example: Permission Slugs

```php
<?php

namespace app\domain\authorization\constants;

/**
 * Constants representing system permission slugs.
 */
final class PermissionSlug
{
	public const DASHBOARD_VIEW = 'dashboard.view';

	public const USERS_VIEW = 'users.view';
	public const USERS_CREATE = 'users.create';
	public const USERS_EDIT = 'users.edit';
	public const USERS_TOGGLE_STATUS = 'users.toggle_status';
	public const USERS_DELETE = 'users.delete';

	public const ROLES_VIEW = 'roles.view';
	public const ROLES_CREATE = 'roles.create';
	public const ROLES_EDIT = 'roles.edit';
	public const ROLES_DELETE = 'roles.delete';
}
```

### Decoupling from Database IDs (Slug-Based Repository Lookups)

Database IDs can shift between environments, seeders, or migrations. Domain logic and Use Cases must **NEVER** hardcode integer primary keys (e.g., `$role_id === 1`). Instead, Repositories provide slug-based lookup methods:

```php
// In Repository Interface:
public function find_by_slug(string $slug): ?Role;

// In Use Case:
$admin_role = $this->role_repository->find_by_slug(RoleSlug::ADMIN);
if ($admin_role !== null) {
	// Dynamically resolve ID from entity instead of hardcoding
	$admin_role_id = $admin_role->get_id();
}
```

## Repository Interface Pattern

Repository Interfaces define persistence contracts and are placed in `repositories/`:

```php
<?php

namespace app\domain\identity\repositories;

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;

/**
 * Repository interface for User persistence.
 */
interface UserRepositoryInterface
{
	/**
	 * Find a user by their ID.
	 *
	 * @param int $id User ID
	 * @return User|null
	 */
	public function find_by_id(int $id): ?User;

	/**
	 * Find a user by their email.
	 *
	 * @param Email $email User email
	 * @return User|null
	 */
	public function find_by_email(Email $email): ?User;

	/**
	 * Create a new user.
	 *
	 * @param User $user User entity to persist
	 * @return User
	 */
	public function create(User $user): User;

	/**
	 * Save (insert or update) a user.
	 *
	 * @param User $user User entity to persist
	 * @return User
	 */
	public function save(User $user): User;

	/**
	 * Delete a user.
	 *
	 * @param User $user User entity to delete
	 * @return User
	 */
	public function delete(User $user): User;
}
```

## Rules

1. **Domain Isolation**: Domain classes MUST NOT depend on CI3 (no `get_instance()`, no `CI_Model`).
2. **Domain Exceptions**: Custom exceptions inherit from `app\domain\exceptions\AppException`.
3. **Encapsulation**: Use `private` properties with getters.
4. **Unified Factory Method**: Entities provide a unified `create()` factory method accepting all properties necessary to exist (with sensible defaults for optional/lifecycle properties). No `from_database()` or `reconstitute()` methods inside domain entities.
5. **Subdirectories**: Value Objects MUST reside in `value_objects/`, Repository Interfaces in `repositories/`, and Domain Constants in `constants/`.
6. **Immutable Value Objects**: Value Objects must be immutable and implement `__toString()`.
7. **Repository Interfaces**: Repository Interfaces define contracts, NOT implementations.
8. **Global Style**: All style rules (PSR-12, docblocks, strict typing) MUST follow the global conventions defined in `GEMINI.md`.
9. **Domain Constants for Fixed Slugs**: System roles, immutable identifiers, and permission slugs MUST be declared as constants in `domain/<context>/constants/<Name>Slug.php` (or `<Name>Constants.php`) instead of using magic strings throughout the codebase.
10. **No Hardcoded Database IDs**: Never couple domain rules, use cases, or controllers to arbitrary autoincrement database IDs (e.g., checking `id === 1` to identify administrators). Use repository slug lookup with Domain Constants (e.g., `$role_repository->find_by_slug(RoleSlug::ADMIN)`).

---

## Anti-Patterns

❌ **Hardcoding Database IDs for Business Roles**: Checking `id === 1` or `role_id === 3` in use cases or controllers. Database IDs are autoincrement implementation details and must not dictate business rules. Always look up or identify roles using `RoleSlug` constants and repository slug finders.
❌ **Database Hydration in Entities (`from_database` / `reconstitute`)**: Entities must NOT contain database hydration methods or reconstitution methods. Converting database DTOs into entities is exclusively the responsibility of Mappers in the Infrastructure/Model layer.
❌ **Root-level Value Objects, Repositories, or Constants**: Placing Value Objects, Repository Interfaces, or Constants directly in `domain/<context>/` instead of `domain/<context>/value_objects/`, `domain/<context>/repositories/`, and `domain/<context>/constants/`.
❌ **Magic Strings for Domain Slugs/Roles**: Writing hardcoded strings (e.g., `'admin'`, `'student'`, `'users.create'`) in controllers, use cases, or models instead of referencing domain constants in `domain/<context>/constants/`.
❌ **Framework Coupling in Domain**: Calling `get_instance()`, `CI_Model`, `CI_Controller`, or database helpers inside Domain classes.
❌ **Using `ModelFactory` in Domain**: Domain entities and value objects must be pure PHP and must never instantiate models.
❌ **Mutable Value Objects**: Adding setters to Value Objects or modifying internal state after construction.
❌ **Untyped Parameters**: Leaving parameters untyped in method signatures or omitting proper Docblocks.
❌ **Portuguese Docblocks**: Writing `@param`, `@return`, or summaries in Portuguese instead of English.
❌ **Single-Letter Variables**: Using single-letter variables (e.g., `$i`, `$k`, `$v`, `$u`) is strictly forbidden, even in loops or tests. Always use descriptive variable names (e.g., `$index`, `$user`, `$key`).
