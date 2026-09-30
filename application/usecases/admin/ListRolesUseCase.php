<?php

namespace app\usecases\admin;

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;

/**
 * Use case for listing all roles.
 */
class ListRolesUseCase
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
	 * @return array<Role> List of all roles
	 */
	public function execute(): array
	{
		return $this->role_repository->find_all();
	}
}
