<?php

namespace app\usecases\admin;

use app\domain\identity\constants\RoleSlug;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\identity\repositories\UserRepositoryInterface;

/**
 * Use case for aggregating operational metrics for the Admin Dashboard.
 */
class GetAdminDashboardMetricsUseCase
{
	/** @var UserRepositoryInterface */
	private $user_repository;

	/** @var RoleRepositoryInterface */
	private $role_repository;

	/**
	 * Constructor.
	 *
	 * @param UserRepositoryInterface $user_repository
	 * @param RoleRepositoryInterface $role_repository
	 */
	public function __construct(UserRepositoryInterface $user_repository, RoleRepositoryInterface $role_repository)
	{
		$this->user_repository = $user_repository;
		$this->role_repository = $role_repository;
	}

	/**
	 * Execute the metrics aggregation.
	 *
	 * @return array<string, int> Operational indicators
	 */
	public function execute(): array
	{
		return [
			'total_students' => $this->user_repository->count_by_role(RoleSlug::STUDENT),
			'total_admins' => $this->user_repository->count_by_role(RoleSlug::ADMIN),
			'total_users' => $this->user_repository->count_all(),
			'total_roles' => count($this->role_repository->find_all()),
		];
	}
}
