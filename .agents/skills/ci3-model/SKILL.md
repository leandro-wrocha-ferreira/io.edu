---
name: ci3-model
description: Use when creating, modifying, or extending CodeIgniter 3 models (infrastructure layer) using MY_Model explicit CRUD engine, KISS entity hydration, and direct query builder filtering following DDD-lite architecture.
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

## Detailed References Index

For in-depth guides, code patterns, and architecture rules, consult the specific reference documentation:

| Topic | Description | Reference Document |
| :--- | :--- | :--- |
| **Entity Hydration & KISS Relations** | Single (`_hydrate_user_roles`) vs batch (`_hydrate_batch_user_roles`) relation hydration. Why clean single-table queries are used instead of `GROUP_CONCAT` anti-patterns. | [repository-hydration.md](references/repository-hydration.md) |
| **Query Builder & DataTables** | Server-side DataTables pagination (`find_paginated`), soft delete handling (`deleted_at`), and permission count queries. | [query-patterns-dt.md](references/query-patterns-dt.md) |

---

## Model Configuration Properties Summary

Every model extending `MY_Model` inherits configuration properties that control its behavior:

| Property | Type | Default | Purpose |
| :--- | :--- | :--- | :--- |
| **`$table`** | `string` | `''` | Name of the database table (e.g. `'users'`, `'roles'`). Always define in child models. |
| **`$primary_key`** | `string` | `'id'` | Primary key column name. |
| **`$entity_class`** | `?string` | `null` | Target Domain Entity class FQCN (e.g. `User::class`). Enables automatic `find_by_id()` and `find_all()` hydration. |

*(Note: The previous global scopes and lifecycle hooks like `$before_get` or `$after_update` have been removed to enforce explicit local filtering.)*

---

## Standard Model Skeleton (KISS Pattern)

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\example\Example;
use app\domain\example\ExampleRepositoryInterface;

/**
 * Example model implementing ExampleRepositoryInterface.
 */
class Example_model extends MY_Model implements ExampleRepositoryInterface
{
	protected string $table = 'examples';
	protected ?string $entity_class = Example::class;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Explicit local filter method (No global scopes allowed).
	 */
	protected function apply_active_filter(): void
	{
		$this->db->where('examples.is_active', 1);
	}

	/**
	 * Find an entity by ID with covariant return type.
	 *
	 * @param int|string $id Entity ID
	 * @return Example|null
	 */
	public function find_by_id(int|string $id): ?Example
	{
		return parent::find_by_id($id);
	}

	/**
	 * Save (insert or update) an entity.
	 *
	 * @param Example $example
	 * @return void
	 */
	public function save(Example $example): void
	{
		$data = [
			'name' => $example->get_name(),
		];

		if ($example->get_id() !== null) {
			$this->update($data, ['id' => $example->get_id()]);
		} else {
			$new_id = $this->insert($data);
			$example->set_id((int) $new_id);
		}
	}

	/**
	 * Delete matching records (soft delete example).
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		return $this->update(['deleted_at' => date('Y-m-d H:i:s')], $where);
	}
}
```

---

## Core Rules & Guidelines

1. **Inheritance**: All models MUST extend `MY_Model` (`application/core/MY_Model.php`).
2. **Interface Implementation**: Models MUST implement their respective Domain Repository Interface (`implements UserRepositoryInterface`).
3. **Strict Typing**: Method signatures MUST be strictly typed in parameters and return types exactly as defined by the Interfaces, and correctly documented in Docblocks. Use manual casts only when strictly necessary.
4. **No `created_at` / `updated_at` in Save**: Timestamps are managed by **database triggers**. Models MUST NOT set these fields in insert/update data arrays.
5. **KISS Over GROUP_CONCAT**: Never use `GROUP_CONCAT`, `ANY_VALUE()`, or JSON string concatenations inside queries to fetch relations. Use clean single-table queries and dedicated relation helpers (`_hydrate_user_roles`, `_hydrate_batch_user_roles`).
6. **Explicit Scopes Only**: Do not use global scopes (no `$before_get` arrays). Apply filters explicitly inside your repository methods by calling internal helpers (e.g., `$this->apply_tenant_filter()`).
7. **No Namespaces**: CI3 models do NOT have a namespace. Use `use app\domain\...` for importing Domain classes.
8. **Code Style**:
   - Tab indentations (`.editorconfig`)
   - PSR-12 bracket style (`{` on the next line for classes and methods)
   - Mandatory English docblocks with `@param` and `@return`
   - **No Vertical Alignment**: Never use extra spaces to align symbols like `=>` or `=`. Use exactly one space before and after the symbol to prevent noisy git diffs.

---

## Anti-Patterns

❌ **Absolute/Machine paths**: Referencing machine-specific URLs like `file:///home/...` instead of relative paths (`references/repository-hydration.md`).
❌ **Setting `created_at` / `updated_at` in Model**: Manually inserting or updating timestamps in `$data` instead of letting database triggers manage them.
❌ **`GROUP_CONCAT` for Relations**: Building complex multi-join SQL queries with `GROUP_CONCAT` and `ANY_VALUE` instead of using KISS relation hydration methods.
❌ **Global Magic Scopes**: Adding `$before_get` or global query interception hooks. Models must apply filters explicitly.
❌ **Untyped Parameters**: Leaving parameters untyped in method signatures or omitting proper Docblocks. Avoid untyped parameters and only use internal casting (e.g., `$id = (int) $id;`) when strictly necessary.
❌ **Vertical Alignment**: Using extra spaces to align `=` or `=>` vertically in arrays or variable assignments.
❌ **Single-Letter Variables**: Using single-letter variables (e.g., `$i`, `$k`, `$v`, `$u`) is strictly forbidden, even in loops or tests. Always use descriptive variable names (e.g., `$index`, `$user`, `$key`).

