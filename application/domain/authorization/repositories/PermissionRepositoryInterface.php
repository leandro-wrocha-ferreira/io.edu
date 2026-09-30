<?php

namespace app\domain\authorization\repositories;

use app\domain\authorization\Permission;

/**
 * Repository interface for Permission persistence.
 */
interface PermissionRepositoryInterface
{
	/**
	 * Find all permissions.
	 *
	 * @return array<Permission> Permission entities
	 */
	public function find_all(): array;

	/**
	 * Find a permission by ID.
	 *
	 * @param int|string $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id(int|string $id): ?Permission;

	/**
	 * Find permissions associated with a specific role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<Permission>
	 */
	public function find_by_role_id(int $role_id): array;

	/**
	 * Find permission IDs associated with a specific role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<int> List of permission IDs
	 */
	public function find_ids_by_role_id(int $role_id): array;

	/**
	 * Sync permissions for a role in the role_permissions association table.
	 *
	 * @param int $role_id Role ID
	 * @param array<int> $permission_ids Permission IDs to associate
	 * @return void
	 */
	public function sync_role_permissions(int $role_id, array $permission_ids): void;

	/**
	 * Check if a role slug has a specific permission slug.
	 *
	 * @param string $role_slug Role slug (e.g. 'admin')
	 * @param string $permission_slug Permission slug (e.g. 'users.create')
	 * @return bool
	 */
	public function has_role_permission(string $role_slug, string $permission_slug): bool;

	/**
	 * Save (insert or update) a permission.
	 *
	 * @param Permission $permission Permission entity to persist
	 * @return Permission
	 */
	public function save(Permission $permission): Permission;

	/**
	 * Delete permissions matching specified filter conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions
	 * @return bool
	 */
	public function delete(array $where): bool;
}
