<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\models\dtos\CourseLessonDatabase;
use app\models\mappers\CourseLessonMapper;

/**
 * Course lesson model implementing CourseLessonRepositoryInterface.
 */
class Course_lesson_model extends MY_Model implements CourseLessonRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'course_lessons';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a lesson by ID.
	 *
	 * @param int $id Lesson ID
	 * @return CourseLesson|null
	 */
	public function find_by_id(int $id): ?CourseLesson
	{
		$row = $this->db
			->where('course_lessons.id', $id)
			->where('course_lessons.deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		return CourseLessonMapper::to_entity(new CourseLessonDatabase($row));
	}

	/**
	 * Find all non-deleted lessons for a module ordered by sort_order ASC.
	 *
	 * @param int $module_id
	 * @param bool $only_published
	 * @return array<CourseLesson>
	 */
	public function find_by_module_id(int $module_id, bool $only_published = false): array
	{
		$this->db
			->where('course_lessons.module_id', $module_id)
			->where('course_lessons.deleted_at IS NULL', null, false);

		if ($only_published) {
			$this->db->where('course_lessons.status', ContentStatus::PUBLISHED);
		}

		$rows = $this->db
			->order_by('course_lessons.sort_order', 'ASC')
			->order_by('course_lessons.id', 'ASC')
			->get($this->table)
			->result_array();

		return CourseLessonMapper::to_entities($rows);
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
		$insert_id = (int) $this->insert($data);

		$created = $this->find_by_id($insert_id);
		return $created ?? $lesson;
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

		$data = CourseLessonMapper::to_database_update($lesson);
		$this->update($data, ['id' => $lesson->get_id()]);

		$updated = $this->find_by_id($lesson->get_id());
		return $updated ?? $lesson;
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
	 * Reorder lessons for a given module.
	 *
	 * @param int $module_id
	 * @param array<int> $lesson_ids
	 * @return void
	 */
	public function reorder(int $module_id, array $lesson_ids): void
	{
		if (empty($lesson_ids)) {
			return;
		}

		$position = 1;
		foreach ($lesson_ids as $lesson_id) {
			$this->db
				->where('id', (int) $lesson_id)
				->where('module_id', $module_id)
				->update($this->table, ['sort_order' => $position]);
			$position++;
		}
	}

	/**
	 * Get next sort order value for a new lesson in a module.
	 *
	 * @param int $module_id
	 * @return int
	 */
	public function get_next_sort_order(int $module_id): int
	{
		$row = $this->db
			->select_max('sort_order', 'max_order')
			->where('module_id', $module_id)
			->where('deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		return isset($row['max_order']) ? ((int) $row['max_order']) + 1 : 1;
	}
}
