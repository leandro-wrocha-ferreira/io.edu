<?php
namespace app\domain\education;

/**
 * Repository interface for CourseClass (Turma) entity persistence.
 */
interface ClassRepositoryInterface
{
	/**
	 * Find a class by ID.
	 *
	 * @param int|string $id
	 * @return CourseClass|null
	 */
	public function find_by_id($id): ?CourseClass;

	/**
	 * Get all classes for a specific course.
	 *
	 * @param int $course_id
	 * @return CourseClass[]
	 */
	public function find_by_course(int $course_id): array;

	/**
	 * Get all classes.
	 *
	 * @return CourseClass[]
	 */
	public function find_all(): array;

	/**
	 * Save a class (insert or update).
	 *
	 * @param CourseClass $class
	 * @return int Class ID
	 */
	public function save(CourseClass $class): int;

	/**
	 * Delete classes matching specified conditions.
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;

	/**
	 * Associate specific lessons with a class and recalculate total duration in seconds.
	 *
	 * @param int $class_id
	 * @param array $lesson_ids
	 * @return int Total duration in seconds of all linked lessons
	 */
	public function sync_lessons(int $class_id, array $lesson_ids): int;
}
