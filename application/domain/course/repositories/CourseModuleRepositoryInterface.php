<?php

namespace app\domain\course\repositories;

use app\domain\course\CourseModule;

/**
 * Repository interface for CourseModule persistence.
 */
interface CourseModuleRepositoryInterface
{
	/**
	 * Find a course module by ID.
	 *
	 * @param int $id Module ID
	 * @return CourseModule|null
	 */
	public function find_by_id(int $id): ?CourseModule;

	/**
	 * Find all non-deleted modules for a specific course ordered by sort_order ASC.
	 *
	 * @param int $course_id
	 * @param bool $only_published
	 * @return array<CourseModule>
	 */
	public function find_by_course_id(int $course_id, bool $only_published = false): array;

	/**
	 * Create a new module.
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function create(CourseModule $module): CourseModule;

	/**
	 * Save (insert or update) a module.
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function save(CourseModule $module): CourseModule;

	/**
	 * Delete a module (soft delete).
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function delete(CourseModule $module): CourseModule;

	/**
	 * Reorder modules for a given course.
	 *
	 * @param int $course_id
	 * @param array<int> $module_ids
	 * @return void
	 */
	public function reorder(int $course_id, array $module_ids): void;

	/**
	 * Get the next sequential sort order for a new module in a course.
	 *
	 * @param int $course_id
	 * @return int
	 */
	public function get_next_sort_order(int $course_id): int;
}
