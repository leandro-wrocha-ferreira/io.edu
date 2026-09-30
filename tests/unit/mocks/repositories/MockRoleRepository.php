<?php

namespace tests\unit\mocks\repositories;

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;

/**
 * Shared Mock repository for Role UseCases testing.
 *
 * Stores roles in-memory and provides find_by_id, find_all,
 * save, delete, find_paginated, and count_all for test isolation.
 */
class MockRoleRepository implements RoleRepositoryInterface
{
	/** @var array<Role> */
	private array $roles = [];

	/**
	 * Find all roles.
	 *
	 * @return array<Role>
	 */
	public function find_all(): array
	{
		return $this->roles;
	}

	/**
	 * Find a role by ID.
	 *
	 * @param int|string $id Role ID
	 * @return Role|null
	 */
	public function find_by_id(int|string $id): ?Role
	{
		$role_id = (int) $id;
		foreach ($this->roles as $role) {
			if ($role->get_id() === $role_id) {
				return $role;
			}
		}
		return null;
	}

	/**
	 * Find a role by unique slug.
	 *
	 * @param string $slug Role slug
	 * @return Role|null
	 */
	public function find_by_slug(string $slug): ?Role
	{
		foreach ($this->roles as $role) {
			if ($role->get_slug() === $slug) {
				return $role;
			}
		}
		return null;
	}

	/**
	 * Save (insert or update) a role.
	 *
	 * @param Role $role Role entity to persist
	 * @return Role
	 */
	public function save(Role $role): Role
	{
		if ($role->get_id() === null) {
			$new_id = count($this->roles) + 1;
			$saved_role = Role::create(
				$role->get_name(),
				$role->get_slug(),
				$role->get_description(),
				$new_id,
				$role->get_created_at(),
				$role->get_updated_at()
			);
			$this->roles[] = $saved_role;
			return $saved_role;
		}

		foreach ($this->roles as $index => $item) {
			if ($item->get_id() === $role->get_id()) {
				$this->roles[$index] = $role;
				return $role;
			}
		}

		$this->roles[] = $role;
		return $role;
	}

	/**
	 * Delete roles matching conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		$role_id = $where['id'] ?? null;
		if ($role_id !== null) {
			$this->roles = array_values(array_filter($this->roles, function (Role $role) use ($role_id) {
				return $role->get_id() !== (int) $role_id;
			}));
		}
		return true;
	}

	/**
	 * Count all roles.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		return count($this->roles);
	}

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
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$filtered = array_filter($this->roles, function (Role $role) use ($search) {
			if ($search !== '') {
				$name_match = stripos($role->get_name(), $search) !== false;
				$slug_match = stripos($role->get_slug(), $search) !== false;
				return $name_match || $slug_match;
			}
			return true;
		});

		$total_filtered = count($filtered);
		$sliced = array_slice(array_values($filtered), $start, $length);

		return [
			'data' => $sliced,
			'recordsFiltered' => $total_filtered,
		];
	}
}
