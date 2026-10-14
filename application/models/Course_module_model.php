<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\models\dtos\CourseModuleDatabase;
use app\models\mappers\CourseModuleMapper;

/**
 * Course module model implementing CourseModuleRepositoryInterface.
 */
class Course_module_model extends MY_Model implements CourseModuleRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'course_modules';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a module by ID.
	 *
	 * @param int $id Module ID
	 * @return CourseModule|null
	 */
	public function find_by_id(int $id): ?CourseModule
	{
		$row = $this->db
			->where('course_modules.id', $id)
			->where('course_modules.deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		return CourseModuleMapper::to_entity(new CourseModuleDatabase($row));
	}

	/**
	 * Find all non-deleted modules for a course ordered by sort_order ASC.
	 *
	 * @param int $course_id
	 * @param bool $only_published
	 * @return array<CourseModule>
	 */
	public function find_by_course_id(int $course_id, bool $only_published = false): array
	{
		$this->db
			->where('course_modules.course_id', $course_id)
			->where('course_modules.deleted_at IS NULL', null, false);

		if ($only_published) {
			$this->db->where('course_modules.status', ContentStatus::PUBLISHED);
		}

		$rows = $this->db
			->order_by('course_modules.sort_order', 'ASC')
			->order_by('course_modules.id', 'ASC')
			->get($this->table)
			->result_array();

		return CourseModuleMapper::to_entities($rows);
	}

	/**
	 * Create a new module.
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function create(CourseModule $module): CourseModule
	{
		$data = CourseModuleMapper::to_database_create($module);
		$insert_id = (int) $this->insert($data);

		$created = $this->find_by_id($insert_id);
		return $created ?? $module;
	}

	/**
	 * Save (insert or update) a module.
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function save(CourseModule $module): CourseModule
	{
		if ($module->get_id() === null) {
			return $this->create($module);
		}

		$data = CourseModuleMapper::to_database_update($module);
		$this->update($data, ['id' => $module->get_id()]);

		$updated = $this->find_by_id($module->get_id());
		return $updated ?? $module;
	}

	/**
	 * Delete a module (soft delete).
	 *
	 * @param CourseModule $module
	 * @return CourseModule
	 */
	public function delete(CourseModule $module): CourseModule
	{
		$module->delete();
		return $this->save($module);
	}

	/**
	 * Reorder modules for a given course.
	 *
	 * @param int $course_id
	 * @param array<int> $module_ids
	 * @return void
	 */
	public function reorder(int $course_id, array $module_ids): void
	{
		if (empty($module_ids)) {
			return;
		}

		$position = 1;
		foreach ($module_ids as $module_id) {
			$this->db
				->where('id', (int) $module_id)
				->where('course_id', $course_id)
				->update($this->table, ['sort_order' => $position]);
			$position++;
		}
	}

	/**
	 * Get next sort order value for a new module.
	 *
	 * @param int $course_id
	 * @return int
	 */
	public function get_next_sort_order(int $course_id): int
	{
		$row = $this->db
			->select_max('sort_order', 'max_order')
			->where('course_id', $course_id)
			->where('deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		return isset($row['max_order']) ? ((int) $row['max_order']) + 1 : 1;
	}
}
