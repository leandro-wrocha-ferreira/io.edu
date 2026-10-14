<?php

namespace tests\unit\mocks\repositories;

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\models\dtos\CourseLessonDatabase;
use app\models\mappers\CourseLessonMapper;

/**
 * Shared mock repository for CourseLesson tests.
 *
 * Simulates raw database storage and validates DTO/Mapper transformations.
 */
class MockCourseLessonRepository implements CourseLessonRepositoryInterface
{
	/**
	 * Simulated raw database rows.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private array $rows = [];

	/**
	 * Auto-increment counter.
	 *
	 * @var int
	 */
	private int $auto_increment = 1;

	/**
	 * Find a lesson by ID.
	 *
	 * @param int $id
	 * @return CourseLesson|null
	 */
	public function find_by_id(int $id): ?CourseLesson
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return CourseLessonMapper::to_entity(new CourseLessonDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find all non-deleted lessons for a module.
	 *
	 * @param int $module_id
	 * @param bool $only_published
	 * @return array<CourseLesson>
	 */
	public function find_by_module_id(int $module_id, bool $only_published = false): array
	{
		$matched = [];
		foreach ($this->rows as $row) {
			if ($row['module_id'] === $module_id && empty($row['deleted_at'])) {
				if ($only_published && $row['status'] !== ContentStatus::PUBLISHED) {
					continue;
				}
				$matched[] = $row;
			}
		}

		usort($matched, static function (array $item_a, array $item_b): int {
			return $item_a['sort_order'] <=> $item_b['sort_order'];
		});

		return CourseLessonMapper::to_entities($matched);
	}

	/**
	 * Create a new lesson.
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function create(CourseLesson $lesson): CourseLesson
	{
		$data = CourseLessonMapper::to_database_create($lesson);
		$insert_id = $this->auto_increment++;

		$data['id'] = $insert_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['deleted_at'] = null;

		$this->rows[$insert_id] = $data;

		return CourseLessonMapper::to_entity(new CourseLessonDatabase($data));
	}

	/**
	 * Save (insert or update) a lesson.
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function save(CourseLesson $lesson): CourseLesson
	{
		if ($lesson->get_id() === null) {
			return $this->create($lesson);
		}

		$existing_id = $lesson->get_id();
		$data = CourseLessonMapper::to_database_update($lesson);

		if (isset($this->rows[$existing_id])) {
			$this->rows[$existing_id] = array_merge($this->rows[$existing_id], $data, [
				'updated_at' => date('Y-m-d H:i:s'),
			]);
		} else {
			$data['id'] = $existing_id;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$this->rows[$existing_id] = $data;
		}

		return CourseLessonMapper::to_entity(new CourseLessonDatabase($this->rows[$existing_id]));
	}

	/**
	 * Delete a lesson (soft delete).
	 *
	 * @param CourseLesson $lesson
	 * @return CourseLesson
	 */
	public function delete(CourseLesson $lesson): CourseLesson
	{
		$lesson->delete();
		return $this->save($lesson);
	}

	/**
	 * Reorder lessons.
	 *
	 * @param int $module_id
	 * @param array<int> $lesson_ids
	 * @return void
	 */
	public function reorder(int $module_id, array $lesson_ids): void
	{
		$position = 1;
		foreach ($lesson_ids as $lesson_id) {
			if (isset($this->rows[$lesson_id]) && $this->rows[$lesson_id]['module_id'] === $module_id) {
				$this->rows[$lesson_id]['sort_order'] = $position;
				$position++;
			}
		}
	}

	/**
	 * Get next sort order value for a module.
	 *
	 * @param int $module_id
	 * @return int
	 */
	public function get_next_sort_order(int $module_id): int
	{
		$max_order = 0;
		foreach ($this->rows as $row) {
			if ($row['module_id'] === $module_id && empty($row['deleted_at'])) {
				if ((int) $row['sort_order'] > $max_order) {
					$max_order = (int) $row['sort_order'];
				}
			}
		}
		return $max_order + 1;
	}
}
