<?php

namespace app\usecases\admin;

use app\domain\authorization\Permission;
use app\domain\authorization\repositories\PermissionRepositoryInterface;

/**
 * Use case for listing all permissions.
 */
class ListPermissionsUseCase
{
	/** @var PermissionRepositoryInterface */
	private $permission_repository;

	/**
	 * Constructor.
	 *
	 * @param PermissionRepositoryInterface $permission_repository
	 */
	public function __construct(PermissionRepositoryInterface $permission_repository)
	{
		$this->permission_repository = $permission_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @return array List of Permission entities
	 */
	public function execute(): array
	{
		return $this->permission_repository->find_all();
	}

	/**
	 * Find permission IDs associated with a specific role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<int>
	 */
	public function get_ids_by_role_id(int $role_id): array
	{
		return $this->permission_repository->find_ids_by_role_id($role_id);
	}
}
