<?php

namespace app\usecases\admin;

use app\domain\authorization\repositories\PermissionRepositoryInterface;
use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;

/**
 * Use case for creating a new role.
 */
class CreateRoleUseCase
{
	/** @var RoleRepositoryInterface */
	private RoleRepositoryInterface $role_repository;

	/** @var PermissionRepositoryInterface */
	private PermissionRepositoryInterface $permission_repository;

	/**
	 * Constructor.
	 *
	 * @param RoleRepositoryInterface $role_repository
	 * @param PermissionRepositoryInterface $permission_repository
	 */
	public function __construct(
		RoleRepositoryInterface $role_repository,
		PermissionRepositoryInterface $permission_repository
	)
	{
		$this->role_repository = $role_repository;
		$this->permission_repository = $permission_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @param string $name Display name
	 * @param string $slug Unique slug
	 * @param string|null $description Optional description
	 * @param array<int> $permission_ids Permission IDs to assign
	 * @return Role Created role entity
	 */
	public function execute(string $name, string $slug, ?string $description = null, array $permission_ids = []): Role
	{
		$role = Role::create($name, $slug, $description);
		$saved_role = $this->role_repository->save($role);

		if (!empty($permission_ids)) {
			$this->permission_repository->sync_role_permissions($saved_role->get_id(), $permission_ids);
		}

		return $saved_role;
	}
}
