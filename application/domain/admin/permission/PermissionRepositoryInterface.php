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
	 * @param int|string $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id($id): ?Permission;

	/**
	 * Save (insert or update) a permission.
	 *
	 * @param Permission $permission
	 * @return void
	 */
	public function save(Permission $permission): void;

	/**
	 * Delete permissions matching specified conditions.
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;
}
