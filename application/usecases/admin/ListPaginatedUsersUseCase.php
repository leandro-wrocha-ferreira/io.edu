<?php

namespace app\usecases\admin;

use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;

/**
 * Use case for listing users with server-side pagination and search.
 */
class ListPaginatedUsersUseCase
{
	/** @var UserRepositoryInterface */
	private UserRepositoryInterface $user_repository;

	/**
	 * Constructor.
	 *
	 * @param UserRepositoryInterface $user_repository
	 */
	public function __construct(UserRepositoryInterface $user_repository)
	{
		$this->user_repository = $user_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<User>, recordsFiltered: int, recordsTotal: int}
	 */
	public function execute(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$result = $this->user_repository->find_paginated($start, $length, $search, $order_col, $order_dir);
		$total = $this->user_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
