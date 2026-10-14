<?php

namespace app\domain\course\repositories;

use app\domain\course\CourseLesson;

/**
 * Repository interface for CourseLesson persistence.
 */
interface CourseLessonRepositoryInterface
{
	/**
	 * Find a course lesson by ID.
	 *
	 * @param int $id Lesson ID
	 * @return CourseLesson|null
	 */
	public function find_by_id(int $id): ?CourseLesson;

	/**
	 * Find all non-deleted lessons for a module ordered by sort_order ASC.
	 *
	 * @param int $module_id
	 * @param bool $only_published
	 * @return array<CourseLesson>
	 */
	public function find_by_module_id(int $module_id, bool $only_published = false): array;

	/**
	 * Create a new lesson.
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function create(CourseLesson $lesson): CourseLesson;

	/**
	 * Save (insert or update) a lesson.
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function save(CourseLesson $lesson): CourseLesson;

	/**
	 * Delete a lesson (soft delete).
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function delete(CourseLesson $lesson): CourseLesson;

	/**
	 * Reorder lessons for a given module.
	 *
	 * @param int $module_id
	 * @param array<int> $lesson_ids
	 * @return void
	 */
	public function reorder(int $module_id, array $lesson_ids): void;

	/**
	 * Get the next sequential sort order for a new lesson in a module.
	 *
	 * @param int $module_id
	 * @return int
	 */
	public function get_next_sort_order(int $module_id): int;
}
