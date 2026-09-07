# Domain Entity Hydration & Relation Patterns (KISS)

## Overview

In DDD-lite architecture, Models bridge database tables and Domain Entities (`app\domain\...`). Models are responsible for retrieving data from database tables and mapping them into rich, encapsulated Domain Entities.

---

## Entity Hydration Basics

Models define `$entity_class` to specify their target Domain Entity:

```php
class User_model extends MY_Model implements UserRepositoryInterface
{
	protected string $table = 'users';
	protected ?string $entity_class = User::class;
}
```

When `$entity_class` is defined, `MY_Model` automatically converts raw database row arrays into Domain Entities using the static factory method `from_database(array $row)` on the entity class. This is used internally by `find_by_id()` and `find_all()`, but you can also use these helpers for custom queries:

- **Single Record**: `to_entity(?array $row): ?object`
- **Multiple Records**: `to_entities(array $rows): array`

---

## The KISS Relation Pattern vs GROUP_CONCAT Anti-Pattern

### ❌ Anti-Pattern: `GROUP_CONCAT` and String Concatenation in Queries

```sql
-- DO NOT DO THIS!
SELECT 
    users.*,
    ANY_VALUE(roles.slug) as role,
    CONCAT('[', GROUP_CONCAT(DISTINCT user_roles.role_id ORDER BY user_roles.role_id SEPARATOR ','), ']') as role_ids
FROM users
LEFT JOIN user_roles ON user_roles.user_id = users.id
LEFT JOIN roles ON roles.id = user_roles.role_id
GROUP BY users.id
```

#### Why `GROUP_CONCAT` is problematic:
1. **Engine Specific**: Requires MySQL/MariaDB syntax (`ANY_VALUE`, `GROUP_CONCAT`); fails or behaves unpredictably in SQLite or other database drivers.
2. **Coupling**: Blurs single-table lookup responsibilities by forcing outer `JOIN`s into every base query.
3. **Brittle String Parsing**: Requires JSON encoding hack strings inside SQL queries.

---

### ✅ KISS Pattern: Clean Single-Table Query + Dedicated Relation Helper

Keep base database queries simple, fast, and unpolluted. Perform a direct table lookup and populate associated relations using dedicated helper methods before hydrating the entity.

#### 1. Single Record Relation Hydration (`_hydrate_user_roles`)

For single lookups (`find_by_email`):

```php
public function find_by_email(Email $email): ?User
{
    // 1. Clean lookup on 'users' table
    $row = $this->db->where('users.email', (string) $email)->get($this->table)->row_array();
    
    if ($row === null) {
        return null;
    }

    // 2. Hydrate roles via clean helper query
    $row = $this->_hydrate_user_roles($row);

    // 3. Hydrate User Domain Entity
    return $this->to_entity($row);
}

private function _hydrate_user_roles(array $row): array
{
    $roles = $this->db
        ->select('roles.id, roles.slug')
        ->join('roles', 'roles.id = user_roles.role_id')
        ->where('user_roles.user_id', (int) $row['id'])
        ->get('user_roles')
        ->result_array();

    $role_ids = array_map('intval', array_column($roles, 'id'));
    $row['role_ids'] = json_encode($role_ids);
    $row['role'] = !empty($roles) ? $roles[0]['slug'] : null;

    return $row;
}
```

#### 2. Batch Relation Hydration (`_hydrate_batch_user_roles`)

For list lookups with pagination or complex filters, avoid $N+1$ queries by fetching raw rows, batch-loading relations for all IDs in a single query, and then using `to_entities`:

```php
public function find_active_users(): array
{
    $this->db
        ->where('users.deleted_at', NULL)
        ->order_by('users.created_at', 'DESC');

    // 1. Fetch raw arrays
    $rows = $this->db->get($this->table)->result_array();
    
    // 2. Hydrate relations in batch
    $rows = $this->_hydrate_batch_user_roles($rows);

    // 3. Convert all arrays to entities
    return $this->to_entities($rows);
}

private function _hydrate_batch_user_roles(array $rows): array
{
    if (empty($rows)) {
        return [];
    }

    $user_ids = array_map('intval', array_column($rows, 'id'));
    $user_ids = array_filter(array_unique($user_ids));

    if (empty($user_ids)) {
        return $rows;
    }

    $roles_data = $this->db
        ->select('user_roles.user_id, roles.id as role_id, roles.slug')
        ->join('roles', 'roles.id = user_roles.role_id')
        ->where_in('user_roles.user_id', $user_ids)
        ->get('user_roles')
        ->result_array();

    $user_roles_map = [];
    foreach ($roles_data as $item) {
        $uid = (int) $item['user_id'];
        if (!isset($user_roles_map[$uid])) {
            $user_roles_map[$uid] = [
                'role_ids' => [],
                'primary_role' => $item['slug'],
            ];
        }
        $user_roles_map[$uid]['role_ids'][] = (int) $item['role_id'];
    }

    foreach ($rows as &$row) {
        $uid = (int) $row['id'];
        if (isset($user_roles_map[$uid])) {
            $row['role_ids'] = json_encode($user_roles_map[$uid]['role_ids']);
            $row['role'] = $user_roles_map[$uid]['primary_role'];
        } else {
            $row['role_ids'] = json_encode([]);
            $row['role'] = null;
        }
    }
    unset($row);

    return $rows;
}
```

---

## Syncing Relation Associations

When persisting entities with many-to-many associations (e.g. `user_roles` or `role_permissions`), use transaction-safe sync helpers inside `save()`:

```php
private function _sync_user_roles(User $user): void
{
    $user_id = $user->get_id();
    if ($user_id === null) {
        return;
    }

    // 1. Delete existing associations
    $this->db->where('user_id', $user_id)->delete('user_roles');

    // 2. Batch insert new associations
    $role_ids = $user->get_role_ids();
    if (!empty($role_ids)) {
        $batch = [];
        foreach ($role_ids as $role_id) {
            $batch[] = [
                'user_id' => $user_id,
                'role_id' => (int) $role_id,
            ];
        }
        $this->db->insert_batch('user_roles', $batch);
    }
}
```
