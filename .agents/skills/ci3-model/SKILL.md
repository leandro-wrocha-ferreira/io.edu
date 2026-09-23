---
name: ci3-model
description: Use when creating, modifying, or extending CodeIgniter 3 models (infrastructure layer) using MY_Model pure data CRUD engine, Mapper translation with Database DTOs, and direct query builder filtering following DDD-lite architecture.
---

# CI3 Model Layer (`MY_Model`, Mappers & Infrastructure Models)

## Overview

In the DDD-lite architecture of the project:
```
Request → Controller → Use Case → Domain → Repository Interface → Model (CI3 Infrastructure)
                                                                     ├── MY_Model (Pure Data CRUD)
                                                                     ├── Database DTO (Row Typing)
                                                                     └── Mapper (Entity Translation)
```

- **Location**:
  - Models: `application/models/` (e.g. `User_model.php`, `Role_model.php`)
  - Mappers: `application/models/mappers/` (e.g. `UserMapper.php`)
  - Database DTOs: `application/models/dtos/` (e.g. `UserDatabase.php`)
- **Base Class**: `application/core/MY_Model.php` (extends `CI_Model`)
- **Naming**: `PascalCase` file name matching class name (e.g. `User_model.php` contains `class User_model extends MY_Model`)
- **Namespace**: **NO namespace** (CI3 loads models via path discovery)
- **Role**: Implement Domain Repository Interfaces (`app\domain\<context>\repositories\...`) and handle database persistence.

---

## Architecture: The Model / DTO / Mapper Pattern

Mappers and DTOs completely decouple the database schema from Domain Entities:

```
[ Database Table ]
       │
       ▼ (Query Builder / MY_Model returns raw array)
[ <Entity>Database DTO ] (Typed database row representation)
       │
       ▼ (<Entity>Mapper::to_entity())
[ Domain Entity ] (Rich business logic, unified create())
       │
       ▼ (<Entity>Mapper::to_database_create() / to_database_update())
[ Database Array Payload ] (For insert/update via MY_Model)
```

1. **`MY_Model` (CRUD Engine)**:
   - Operates purely with raw arrays and scalars (`find_by_id`, `find_all`, `insert`, `update`, `destroy`, `count_all`).
   - Automatically writes audit logs to the `logs` table.
   - Does **NOT** contain entity hydration logic (no `$entity_class`, no `from_database()`, no `to_entity()`).

2. **Database DTO (`application/models/dtos/<Entity>Database.php`)**:
   - Strongly typed `final class` representing the exact schema returned by the database table.
   - Instantiated from raw database row data:
   ```php
   <?php

   final class UserDatabase
   {
   	public int $id;
   	public string $name;
   	public string $email;
   	public string $password;
   	public int $is_active;
   	public DateTime $created_at;
   	public DateTime $updated_at;
   	public ?DateTime $deleted_at;

   	public function __construct(int $id, string $name, string $email, string $password, int $is_active, string $created_at, string $updated_at, ?string $deleted_at)
   	{
   		$this->id = $id;
   		$this->name = $name;
   		$this->email = $email;
   		$this->password = $password;
   		$this->is_active = $is_active;
   		$this->created_at = new DateTime($created_at);
   		$this->updated_at = new DateTime($updated_at);
   		$this->deleted_at = !empty($deleted_at) ? new DateTime($deleted_at) : null;
   	}
   }
   ```

3. **Mapper (`application/models/mappers/<Entity>Mapper.php`)**:
   - Converts between `<Entity>Database` DTO and Domain Entity:
   ```php
   <?php

   use app\domain\identity\User;
   use app\domain\identity\value_objects\Email;
   use app\domain\identity\value_objects\Password;

   class UserMapper
   {
   	public static function to_entity(?UserDatabase $row): ?User
   	{
   		if (empty($row)) {
   			return null;
   		}

   		return User::create(
   			$row->name,
   			new Email($row->email),
   			Password::from_hash($row->password),
   			$row->id ?? null,
   			(bool) $row->is_active,
   			$row->created_at,
   			$row->updated_at,
   			$row->deleted_at ?? null
   		);
   	}

   	public static function to_database_create(User $user): array
   	{
   		return [
   			'name' => $user->get_name(),
   			'email' => (string) $user->get_email(),
   			'password' => (string) $user->get_password(),
   			'is_active' => $user->is_active() ? 1 : 0,
   		];
   	}

   	public static function to_database_update(User $user): array
   	{
   		return [
   			'name' => $user->get_name(),
   			'email' => (string) $user->get_email(),
   			'password' => (string) $user->get_password(),
   			'is_active' => $user->is_active() ? 1 : 0,
   			'deleted_at' => $user->get_deleted_at() ?? null,
   		];
   	}
   }
   ```

---

## Standard Model Skeleton

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\identity\value_objects\Email;

/**
 * Example model implementing ExampleRepositoryInterface.
 */
class User_model extends MY_Model implements UserRepositoryInterface
{
	protected string $table = 'users';

	public function __construct()
	{
		parent::__construct();
	}

	public function find_by_id(int $id): ?User
	{
		$row = parent::find_by_id($id);
		if ($row === null) {
			return null;
		}

		return UserMapper::to_entity($row);
	}

	public function find_by_email(Email $email): ?User
	{
		$row = $this->db
			->where('users.email', (string) $email)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		return UserMapper::to_entity($row);
	}

	public function create(User $user): User
	{
		$data = UserMapper::to_database_create($user);
		$insert_id = (int) $this->insert($data);

		$row = $this->db
			->where($this->primary_key, $insert_id)
			->get($this->table)
			->row_array();

		return UserMapper::to_entity($row);
	}

	public function save(User $user): User
	{
		if ($user->get_id() === null) {
			return $this->create($user);
		}

		$data = UserMapper::to_database_update($user);
		$this->update($data, ['id' => $user->get_id()]);

		$row = $this->db
			->where($this->primary_key, $user->get_id())
			->get($this->table)
			->row_array();

		return UserMapper::to_entity($row) ?? $user;
	}

	public function delete(User $user): User
	{
		$user->delete();
		return $this->save($user);
	}
}
```

---

## Core Rules & Guidelines

1. **Inheritance**: All models MUST extend `MY_Model` (`application/core/MY_Model.php`).
2. **Interface Implementation**: Models MUST implement their respective Domain Repository Interface from `app\domain\<context>\repositories\<Entity>RepositoryInterface`.
3. **Delegation to Mappers**: Models DO NOT perform entity instantiation or mapping directly; they delegate to `<Entity>Mapper` and use `<Entity>Database` DTOs.
4. **No `created_at` / `updated_at` in Payloads**: Timestamps are managed by **database triggers**. Mappers and Models MUST NOT set these fields in insert/update data arrays.
5. **KISS Over GROUP_CONCAT**: Never use `GROUP_CONCAT`, `ANY_VALUE()`, or JSON string concatenations inside queries to fetch relations. Use clean single-table queries and dedicated relation helpers.
6. **Explicit Scopes Only**: Do not use global magic scopes. Apply filters explicitly inside repository methods.
7. **No Namespaces in Models**: CI3 models do NOT have a namespace. Use `use app\domain\...` for importing Domain classes.
8. **Global Style**: All style rules (PSR-12, docblocks, strict typing, vertical alignment, no single-letter variables) MUST follow the global conventions defined in `GEMINI.md`.

---

## Anti-Patterns

❌ **Entity Hydration in `MY_Model`**: Adding `$entity_class` or `to_entity()` to `MY_Model`. `MY_Model` is strictly for data access and auditing.
❌ **Direct Entity Instantiation in Model**: Calling entity constructors or `from_database()` inside the model instead of delegating to `<Entity>Mapper`.
❌ **Setting `created_at` / `updated_at` in Model/Mapper**: Manually inserting or updating timestamps in `$data` instead of letting database triggers manage them.
❌ **`GROUP_CONCAT` for Relations**: Building complex multi-join SQL queries with `GROUP_CONCAT` and `ANY_VALUE` instead of using KISS relation hydration methods.
❌ **Global Magic Scopes**: Adding `$before_get` or global query interception hooks. Models must apply filters explicitly.
❌ **Untyped Parameters**: Leaving parameters untyped in method signatures or omitting proper Docblocks.
