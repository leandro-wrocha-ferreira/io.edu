<?php

namespace app\domain\admin\role;

/**
 * Repository interface for Role persistence.
 */
interface RoleRepositoryInterface
{
	/**
	 * Find all roles with their associated permission IDs.
	 *
	 * @return array Role entities
	 */
	public function find_all(): array;

	/**
	 * Find a role by ID.
	 *
	 * @param int|string $id Role ID
	 * @return Role|null
	 */
	public function find_by_id($id): ?Role;

	/**
	 * Save (insert or update) a role.
	 *
	 * Also syncs the associated permissions in role_permissions.
	 *
	 * @param Role $role
	 * @return void
	 */
	public function save(Role $role): void;

	/**
	 * Delete roles matching specified conditions.
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;
}
