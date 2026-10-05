<?php

namespace app\usecases\course;

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;

/**
 * Use case for listing categories with server-side pagination and search.
 */
class ListPaginatedCategoriesUseCase
{
	/** @var CategoryRepositoryInterface */
	private CategoryRepositoryInterface $category_repository;

	/**
	 * Constructor.
	 *
	 * @param CategoryRepositoryInterface $category_repository
	 */
	public function __construct(CategoryRepositoryInterface $category_repository)
	{
		$this->category_repository = $category_repository;
	}

	/**
	 * Execute paginated search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Column name
	 * @param string $order_dir Direction (ASC/DESC)
	 * @return array{data: array<Category>, recordsFiltered: int, recordsTotal: int}
	 */
	public function execute(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$result = $this->category_repository->find_paginated($start, $length, $search, $order_col, $order_dir);
		$total = $this->category_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
