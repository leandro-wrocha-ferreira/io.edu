<?php

namespace app\domain\identity\repositories;

use app\domain\identity\Role;

/**
 * Repository interface for Role persistence.
 */
interface RoleRepositoryInterface
{
	/**
	 * Find all roles.
	 *
	 * @return array<Role> Role entities
	 */
	public function find_all(): array;

	/**
	 * Find a role by ID.
	 *
	 * @param int|string $id Role ID
	 * @return Role|null
	 */
	public function find_by_id(int|string $id): ?Role;

	/**
	 * Find a role by unique slug.
	 *
	 * @param string $slug Role slug
	 * @return Role|null
	 */
	public function find_by_slug(string $slug): ?Role;

	/**
	 * Save (insert or update) a role.
	 *
	 * @param Role $role Role entity to persist
	 * @return Role Persisted role entity
	 */
	public function save(Role $role): Role;

	/**
	 * Delete roles matching specified conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;

	/**
	 * Count all roles.
	 *
	 * @return int
	 */
	public function count_all(): int;

	/**
	 * Find roles for server-side DataTables with search, order, and pagination.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<Role>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array;
}
