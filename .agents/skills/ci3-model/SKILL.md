---
name: ci3-model
description: Use when creating, modifying, or extending CodeIgniter 3 models (infrastructure layer) using MY_Model lifecycle engine, global query scopes, and CRUD wrappers following DDD-lite architecture.
---

# CI3 Model Layer (`MY_Model` & Infrastructure Models)

## Overview

In the DDD-lite architecture of the project:
```
Request → Controller → Use Case → Domain → Repository Interface → Model (CI3 Infrastructure)
```

- **Location**: `application/models/` (e.g. `User_model.php`, `Role_model.php`, `Permission_model.php`)
- **Base Class**: `application/core/MY_Model.php` (extends `CI_Model`)
- **Naming**: `PascalCase` file name matching class name (e.g. `User_model.php` contains `class User_model extends MY_Model`)
- **Namespace**: **NO namespace** (CI3 loads models via path discovery)
- **Role**: Implement Domain Repository Interfaces (e.g. `implements UserRepositoryInterface`) and handle database persistence.

---

## `MY_Model` Properties Reference

Every model extending `MY_Model` inherits configuration properties that control its behavior:

| Property | Type | Default | Purpose | When to Use |
| :--- | :--- | :--- | :--- | :--- |
| **`$table`** | `string` | `''` | Name of the database table (e.g. `'users'`, `'roles'`). | Always define in child models. |
| **`$primary_key`** | `string` | `'id'` | Primary key column name. | Customize if the table uses a non-standard PK (e.g. `'uuid'`, `'code'`). |
| **`$before_get`** | `array` | `[]` | Methods called automatically before `get_all()`, `get_by_id()`, `get_by()`, and `count_all()`. | Use to define **Global Scopes** (e.g. tenant isolation, excluding protected roles, default ordering). |
| **`$after_get`** | `array` | `[]` | Methods called automatically after SELECT queries. | Use for row transformation, hydration, or read analytics. |
| **`$before_create`** | `array` | `[]` | Methods called before `insert()` or `insert_batch()`. | Use for pre-insert data sanitization or business validations. |
| **`$after_create`** | `array` | `[]` | Methods called after successful insert. | Use for audit logging or dispatching async notifications. |
| **`$before_update`** | `array` | `[]` | Methods called before `update_record()` or `update_by()`. | Use for pre-update checks. |
| **`$after_update`** | `array` | `[]` | Methods called after successful update. | Use for audit logging (recording changed fields). |
| **`$before_delete`** | `array` | `[]` | Methods called before `delete_record()` or `delete_by()`. | Use for cascading cleanup or pre-delete validation. |
| **`$after_delete`** | `array` | `[]` | Methods called after successful delete. | Use for audit logging of deleted IDs. |

---

## Global Query Scopes Pattern

### 1. Declaring a Global Scope
Global Scopes are defined by adding the method name to `$before_get` in your model and implementing a `protected` method:

```php
class User_model extends MY_Model implements UserRepositoryInterface
{
	protected string $table = 'users';

	// Register global scopes to execute automatically before any GET/COUNT
	protected array $before_get = ['scope_exclude_admin_master'];

	/**
	 * Automatically applied before every SELECT query.
	 * Developer never needs to call this manually!
	 */
	protected function scope_exclude_admin_master(): void
	{
		$this->db->group_start()
			->where('roles.slug IS NULL', NULL, FALSE)
			->or_where('roles.slug !=', 'admin-master')
			->group_end();
	}
}
```

### 2. Bypassing Global Scopes (When Needed)
When an administrative task explicitly needs to query all records (including filtered/protected ones), use the chainable scope bypass methods:

```php
// Bypass a specific scope for the next query:
$all_users = $this->user_model->without_scope('scope_exclude_admin_master')->find_all();

// Bypass ALL global scopes for the next query:
$all_users = $this->user_model->without_global_scopes()->find_all();
```
*Note: Scope overrides automatically reset back to active immediately after the query finishes.*

---

## Query Execution Methods

### 1. Standard CRUD Methods (Inherited from `MY_Model`)
For straightforward operations, use the built-in CRUD methods:

```php
// Fetch all records
$rows = $this->get_all();

// Fetch one record by conditions
$row = $this->get_by(['email' => $email]);

// Fetch one record by ID
$row = $this->get_by_id($id);

// Count
$total = $this->count_all();
$active_total = $this->count_by(['is_active' => 1]);

// Insert
$new_id = $this->insert(['name' => 'John', 'email' => 'john@test.com']);

// Update
$this->update_record($id, ['name' => 'John Doe']);
$this->update_by(['email' => $email], ['is_active' => 0]);

// Delete
$this->delete_record($id);
$this->delete_by(['is_active' => 0]);
```

### 2. Composing Complex Queries with Query Builder
For queries involving joins, groupings, or server-side DataTables pagination, chain native CI3 Query Builder clauses before calling the terminal `MY_Model` methods (`get_all()`, `get_by_id()`, `count_all()`). This ensures **global scopes**, **lifecycle callbacks**, and **automatic error logging** continue to run seamlessly without needing closures:

```php
public function find_all(): array
{
	$this->db
		->select('users.*, roles.name as role_name')
		->join('user_roles', 'user_roles.user_id = users.id', 'left')
		->join('roles', 'roles.id = user_roles.role_id', 'left')
		->where('users.deleted_at', NULL)
		->order_by('users.created_at', 'DESC');

	$rows = $this->get_all(); // Executes before_get scopes & error logging automatically!

	return array_map(function (array $row) {
		return User::from_database($row);
	}, $rows);
}
```

---

## Automatic Error Logging

All queries run via standard CRUD methods (`get_all`, `get_by_id`, `insert`, `update_record`, `delete_record`) are protected by `_run_pipeline()`. If any database query fails or throws an exception:
1. The error details, SQL query string (`$this->db->last_query()`), target table, and stack trace are logged automatically via `log_message('error', ...)`.
2. The exception is rethrown to allow the presentation layer (`MY_Controller::_remap()`) to handle it gracefully.

---

## How to Create a New Model

1. Create `application/models/Example_model.php`.
2. Extend `MY_Model` and implement its corresponding Domain Repository Interface:
   ```php
   <?php
   defined('BASEPATH') OR exit('No direct script access allowed');

   use app\domain\course\Course;
   use app\domain\course\CourseRepositoryInterface;

   /**
    * Course model implementing CourseRepositoryInterface.
    */
   class Course_model extends MY_Model implements CourseRepositoryInterface
   {
   	protected string $table = 'courses';
   	protected array $before_get = ['scope_published_only'];

   	public function __construct()
   	{
   		parent::__construct();
   	}

   	protected function scope_published_only(): void
   	{
   		$this->db->where('courses.is_published', 1);
   	}

   	public function find_by_id(int $id): ?Course
   	{
   		$row = $this->get_by_id($id);
   		return $row ? Course::from_database($row) : null;
   	}

   	public function save(Course $course): void
   	{
   		$data = [
   			'title' => $course->get_title(),
   			'slug' => $course->get_slug(),
   		];

   		if ($course->get_id() !== null) {
   			$this->update_record($course->get_id(), $data);
   		} else {
   			$new_id = $this->insert($data);
   			$course->set_id((int) $new_id);
   		}
   	}

   	public function delete(int $id): void
   	{
   		$this->delete_record($id);
   	}
   }
   ```

---

## Rules & Conventions

1. **Inheritance**: All models MUST extend `MY_Model` (in `application/core/MY_Model.php`).
2. **Interface Implementation**: Models MUST implement their respective Domain Repository Interface (`implements UserRepositoryInterface`).
3. **No `created_at` / `updated_at`**: Timestamps are managed by **database triggers**. Models MUST NOT set these fields in insert/update arrays.
4. **No Direct Scope Invocations**: Do NOT write manual helper calls like `_prepare_scopes()`. Register scope methods in `$before_get` instead.
5. **No Namespaces**: CI3 models do NOT have a namespace. Use `use app\domain\...` for importing Domain classes.
6. **Code Style**:
   - Tab indentations (`.editorconfig`)
   - PSR-12 bracket style (`{` on the next line for classes and methods)
   - Mandatory English docblocks with `@param` and `@return`
