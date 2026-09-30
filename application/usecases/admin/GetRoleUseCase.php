<?php

namespace app\usecases\admin;

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for retrieving a single role by ID.
 */
class GetRoleUseCase
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
	 * @return Role Role entity
	 * @throws NotFoundException When role not found
	 */
	public function execute(int $role_id): Role
	{
		$role = $this->role_repository->find_by_id($role_id);
		if ($role === null) {
			throw new NotFoundException("Role not found");
		}

		return $role;
	}
}
