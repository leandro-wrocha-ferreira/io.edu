<?php

namespace app\domain\course\repositories;

use app\domain\course\Course;
use app\domain\course\value_objects\CourseSlug;

/**
 * Repository interface for Course persistence.
 */
interface CourseRepositoryInterface
{
	/**
	 * Find a course by its ID.
	 *
	 * @param int $id Course ID
	 * @return Course|null
	 */
	public function find_by_id(int $id): ?Course;

	/**
	 * Find a course by its unique slug.
	 *
	 * @param CourseSlug $slug
	 * @return Course|null
	 */
	public function find_by_slug(CourseSlug $slug): ?Course;

	/**
	 * Find all non-deleted courses.
	 *
	 * @return array<Course>
	 */
	public function find_all(): array;

	/**
	 * Find courses with server-side pagination, search, and filters.
	 *
	 * @param int $start
	 * @param int $length
	 * @param string $search
	 * @param string $order_col
	 * @param string $order_dir
	 * @param int|null $category_id
	 * @param string|null $status
	 * @return array{data: array<Course>, recordsFiltered: int}
	 */
	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $category_id = null,
		?string $status = null
	): array;

	/**
	 * Count total non-deleted courses.
	 *
	 * @return int
	 */
	public function count_all(): int;

	/**
	 * Count non-deleted courses associated with a specific category.
	 *
	 * @param int $category_id
	 * @return int
	 */
	public function count_by_category_id(int $category_id): int;

	/**
	 * Create a new course record.
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function create(Course $course): Course;

	/**
	 * Save (insert or update) a course.
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function save(Course $course): Course;

	/**
	 * Delete a course (soft delete).
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function delete(Course $course): Course;
}
