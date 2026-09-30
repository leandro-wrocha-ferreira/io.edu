<?php

namespace app\usecases\admin;

use app\domain\authorization\repositories\PermissionRepositoryInterface;
use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for updating an existing role.
 */
class UpdateRoleUseCase
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
	 * @param int $role_id Role ID
	 * @param string $name Display name
	 * @param string $slug Unique slug
	 * @param string|null $description Optional description
	 * @param array<int> $permission_ids Permission IDs to assign
	 * @return Role Updated role entity
	 * @throws NotFoundException When role not found
	 * @throws ConflictException When attempting to update protected role
	 */
	public function execute(int $role_id, string $name, string $slug, ?string $description = null, array $permission_ids = []): Role
	{
		$role = $this->role_repository->find_by_id($role_id);
		if ($role === null) {
			throw new NotFoundException("Perfil não encontrado");
		}

		if ($role->get_slug() === 'admin-master') {
			throw new ConflictException("O perfil AdminMaster é protegido e não pode ser alterado.");
		}

		$role->set_name($name);
		$role->set_slug($slug);
		$role->set_description($description);

		$saved_role = $this->role_repository->save($role);

		$this->permission_repository->sync_role_permissions($saved_role->get_id(), $permission_ids);

		return $saved_role;
	}
}
