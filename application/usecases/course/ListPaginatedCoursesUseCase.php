<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;

/**
 * Use case for listing courses with server-side pagination, search, and filters.
 */
class ListPaginatedCoursesUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseRepositoryInterface $course_repository
	 */
	public function __construct(CourseRepositoryInterface $course_repository)
	{
		$this->course_repository = $course_repository;
	}

	/**
	 * Execute paginated search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Column name
	 * @param string $order_dir Direction (ASC/DESC)
	 * @param int|null $category_id Category ID filter
	 * @param string|null $status Status filter
	 * @return array{data: array<Course>, recordsFiltered: int, recordsTotal: int}
	 */
	public function execute(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $category_id = null,
		?string $status = null
	): array
	{
		$result = $this->course_repository->find_paginated(
			$start,
			$length,
			$search,
			$order_col,
			$order_dir,
			$category_id,
			$status
		);
		$total = $this->course_repository->count_all();

		return [
			'data' => $result['data'],
			'recordsFiltered' => $result['recordsFiltered'],
			'recordsTotal' => $total,
		];
	}
}
