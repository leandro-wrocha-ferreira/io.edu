<?php

namespace app\domain\admin\permission;

/**
 * Repository interface for Permission persistence.
 */
interface PermissionRepositoryInterface
{
	/**
	 * Find all permissions.
	 *
	 * @return array Permission entities
	 */
	public function find_all(): array;

	/**
	 * Find a permission by ID.
	 *
	 * @param int $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id(int $id): ?Permission;

	/**
	 * Save (insert or update) a permission.
	 *
	 * @param Permission $permission
	 * @return void
	 */
	public function save(Permission $permission): void;

	/**
	 * Delete a permission by ID.
	 *
	 * @param int $id
	 * @return void
	 */
	public function delete(int $id): void;
}
