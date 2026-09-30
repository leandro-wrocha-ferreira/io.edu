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
}
