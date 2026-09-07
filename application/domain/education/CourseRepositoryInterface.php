<?php
namespace app\domain\education;

/**
 * Repository interface for Course entity persistence.
 */
interface CourseRepositoryInterface
{
	/**
	 * Find a course by ID.
	 *
	 * @param int|string $id
	 * @return Course|null
	 */
	public function find_by_id($id): ?Course;

	/**
	 * Get all courses.
	 *
	 * @return Course[]
	 */
	public function find_all(): array;

	/**
	 * Save a course (insert or update).
	 *
	 * @param Course $course
	 * @return int Course ID
	 */
	public function save(Course $course): int;

	/**
	 * Delete courses matching specified conditions.
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;
}
