<?php

namespace app\usecases\admin;

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\identity\constants\RoleSlug;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for deleting a role.
 */
class DeleteRoleUseCase
{
	/** @var RoleRepositoryInterface */
	private RoleRepositoryInterface $role_repository;

	/**
	 * Constructor.
	 *
	 * @param RoleRepositoryInterface $role_repository
	 */
	public function __construct(RoleRepositoryInterface $role_repository)
	{
		$this->role_repository = $role_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @param int $role_id Role ID
	 * @return void
	 * @throws NotFoundException When role not found
	 * @throws ConflictException When attempting to delete protected roles
	 */
	public function execute(int $role_id): void
	{
		$role = $this->role_repository->find_by_id($role_id);
		if ($role === null) {
			throw new NotFoundException("Role not found");
		}

		if ($role->get_slug() === 'admin-master' || in_array($role->get_slug(), RoleSlug::ALL, true)) {
			throw new ConflictException("Default system roles cannot be deleted");
		}

		$this->role_repository->delete(['id' => $role_id]);
	}
}
