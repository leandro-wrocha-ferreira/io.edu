<?php

namespace tests\unit\mocks\repositories;

use app\domain\authorization\Permission;
use app\domain\authorization\repositories\PermissionRepositoryInterface;

/**
 * Shared Mock repository for Permission UseCases testing.
 *
 * Stores permissions in-memory and provides find_all, find_by_id,
 * save, delete, and role-permission association operations.
 */
class MockPermissionRepository implements PermissionRepositoryInterface
{
	/** @var array<Permission> */
	private array $permissions = [];

	/** @var array<int, array<int>> Map of role_id => array<permission_id> */
	private array $role_permissions = [];

	/**
	 * Find all permissions.
	 *
	 * @return array<Permission>
	 */
	public function find_all(): array
	{
		return $this->permissions;
	}

	/**
	 * Find a permission by ID.
	 *
	 * @param int|string $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id(int|string $id): ?Permission
	{
		$permission_id = (int) $id;
		foreach ($this->permissions as $permission) {
			if ($permission->get_id() === $permission_id) {
				return $permission;
			}
		}
		return null;
	}

	/**
	 * Save (insert or update) a permission.
	 *
	 * @param Permission $permission
	 * @return Permission
	 */
	public function save(Permission $permission): Permission
	{
		if ($permission->get_id() === null) {
			$new_id = count($this->permissions) + 1;
			$saved = Permission::create(
				$permission->get_name(),
				$permission->get_slug(),
				$permission->get_description(),
				$new_id,
				$permission->get_created_at(),
				$permission->get_updated_at()
			);
			$this->permissions[] = $saved;
			return $saved;
		}

		foreach ($this->permissions as $index => $item) {
			if ($item->get_id() === $permission->get_id()) {
				$this->permissions[$index] = $permission;
				return $permission;
			}
		}

		$this->permissions[] = $permission;
		return $permission;
	}

	/**
	 * Find permissions by Role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<Permission>
	 */
	public function find_by_role_id(int $role_id): array
	{
		$perm_ids = $this->role_permissions[$role_id] ?? [];
		return array_values(array_filter($this->permissions, function (Permission $permission) use ($perm_ids) {
			return in_array($permission->get_id(), $perm_ids, true);
		}));
	}

	/**
	 * Find permission IDs associated with a specific role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<int> List of permission IDs
	 */
	public function find_ids_by_role_id(int $role_id): array
	{
		return $this->role_permissions[$role_id] ?? [];
	}

	/**
	 * Sync permissions for a role in the role_permissions association table.
	 *
	 * @param int $role_id Role ID
	 * @param array<int> $permission_ids Permission IDs to associate
	 * @return void
	 */
	public function sync_role_permissions(int $role_id, array $permission_ids): void
	{
		$this->role_permissions[$role_id] = array_values(array_map('intval', $permission_ids));
	}

	/**
	 * Check if a role slug has a specific permission slug.
	 *
	 * @param string $role_slug Role slug
	 * @param string $permission_slug Permission slug
	 * @return bool
	 */
	public function has_role_permission(string $role_slug, string $permission_slug): bool
	{
		return true;
	}

	/**
	 * Delete permissions matching conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		$permission_id = $where['id'] ?? null;
		if ($permission_id !== null) {
			$this->permissions = array_values(array_filter($this->permissions, function (Permission $permission) use ($permission_id) {
				return $permission->get_id() !== (int) $permission_id;
			}));
		}
		return true;
	}
}
