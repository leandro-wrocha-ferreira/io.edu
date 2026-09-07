# Query Builder, DataTables Pagination & Soft Deletes

## Overview

Infrastructure models implement specific query patterns for DataTables server-side pagination, soft deletes, permission checks, and aggregation.

---

## Server-Side DataTables Pagination (`find_paginated`)

DataTables AJAX requests require returning structured pagination data:
- `data`: Array of hydrated records for the current page
- `recordsFiltered`: Total number of records matching the search criteria

### Implementation Pattern:

```php
/**
 * Find records for server-side DataTables with search, order, and pagination.
 *
 * @param int $start Offset
 * @param int $length Page size
 * @param string $search Global search term
 * @param string $order_col Column name to order by
 * @param string $order_dir ASC or DESC
 * @return array ['data' => array, 'recordsFiltered' => int]
 */
public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
{
	$total = $this->_count_paginated($search);

	$allowed = ['id', 'name', 'email', 'created_at'];
	$col = in_array($order_col, $allowed) ? 'users.' . $order_col : 'users.created_at';
	$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

	$this->db->where('users.deleted_at', NULL);

	if ($search !== '') {
		$this->db->group_start()
			->like('users.name', $search)
			->or_like('users.email', $search)
			->group_end();
	}

	$this->db->order_by($col, $dir)->limit($length, $start);

	$rows = $this->db->get($this->table)->result_array();
	$rows = $this->_hydrate_batch_user_roles($rows);

	return ['data' => $rows, 'recordsFiltered' => $total];
}

/**
 * Count filtered records for DataTables pagination.
 *
 * @param string $search Global search term
 * @return int
 */
private function _count_paginated(string $search): int
{
	$this->db->where('users.deleted_at', NULL);

	if ($search !== '') {
		$this->db->group_start()
			->like('users.name', $search)
			->or_like('users.email', $search)
			->group_end();
	}

	return parent::count_all();
}
```

---

## Soft Deletes Pattern

Models handling soft-deleted records track state via `deleted_at` timestamps instead of executing physical SQL `DELETE` queries.

### Implementation:

```php
/**
 * Soft delete a record by ID.
 *
 * Sets deleted_at timestamp instead of removing record.
 */
public function delete(int $id): void
{
	$this->update(['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);
}
```

### Filtering Active Records:
Always append `$this->db->where('table.deleted_at', NULL)` in list and count queries.

---

## Permission Checks (`has_permission`)

To verify directly in the database whether a user holds a permission without loading full role trees:

```php
public function has_permission(int $user_id, string $permission_slug): bool
{
	$count = (int) $this->db
		->join('user_roles', 'user_roles.user_id = users.id')
		->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
		->join('permissions', 'permissions.id = role_permissions.permission_id')
		->where('users.id', $user_id)
		->where('permissions.slug', $permission_slug)
		->count_all_results('users');

	return $count > 0;
}
```
