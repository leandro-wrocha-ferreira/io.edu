<?php

namespace app\usecases\admin;

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;

/**
 * Use case for listing roles with server-side pagination and search.
 */
class ListPaginatedRolesUseCase
{
	/** @var RoleRepositoryInterface */
	private $role_repository;

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
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<Role>, recordsFiltered: int, recordsTotal: int}
	 */
	public function execute(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$result = $this->role_repository->find_paginated($start, $length, $search, $order_col, $order_dir);
		$total = $this->role_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
