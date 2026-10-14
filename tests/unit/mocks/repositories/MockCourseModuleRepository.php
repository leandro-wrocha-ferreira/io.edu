<?php

namespace tests\unit\mocks\repositories;

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\models\dtos\CourseModuleDatabase;
use app\models\mappers\CourseModuleMapper;

/**
 * Shared mock repository for CourseModule tests.
 *
 * Simulates raw database storage and validates DTO/Mapper transformations.
 */
class MockCourseModuleRepository implements CourseModuleRepositoryInterface
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
	 * Find a module by ID.
	 *
	 * @param int $id
	 * @return CourseModule|null
	 */
	public function find_by_id(int $id): ?CourseModule
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return CourseModuleMapper::to_entity(new CourseModuleDatabase($row));
			}
		}
		return null;
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
		$matched = [];
		foreach ($this->rows as $row) {
			if ($row['course_id'] === $course_id && empty($row['deleted_at'])) {
				if ($only_published && $row['status'] !== ContentStatus::PUBLISHED) {
					continue;
				}
				$matched[] = $row;
			}
		}

		usort($matched, static function (array $item_a, array $item_b): int {
			return $item_a['sort_order'] <=> $item_b['sort_order'];
		});

		return CourseModuleMapper::to_entities($matched);
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
		$insert_id = $this->auto_increment++;

		$data['id'] = $insert_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['deleted_at'] = null;

		$this->rows[$insert_id] = $data;

		return CourseModuleMapper::to_entity(new CourseModuleDatabase($data));
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

		$existing_id = $module->get_id();
		$data = CourseModuleMapper::to_database_update($module);

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

		return CourseModuleMapper::to_entity(new CourseModuleDatabase($this->rows[$existing_id]));
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
	 * Reorder modules.
	 *
	 * @param int $course_id
	 * @param array<int> $module_ids
	 * @return void
	 */
	public function reorder(int $course_id, array $module_ids): void
	{
		$position = 1;
		foreach ($module_ids as $module_id) {
			if (isset($this->rows[$module_id]) && $this->rows[$module_id]['course_id'] === $course_id) {
				$this->rows[$module_id]['sort_order'] = $position;
				$position++;
			}
		}
	}

	/**
	 * Get next sort order value.
	 *
	 * @param int $course_id
	 * @return int
	 */
	public function get_next_sort_order(int $course_id): int
	{
		$max_order = 0;
		foreach ($this->rows as $row) {
			if ($row['course_id'] === $course_id && empty($row['deleted_at'])) {
				if ((int) $row['sort_order'] > $max_order) {
					$max_order = (int) $row['sort_order'];
				}
			}
		}
		return $max_order + 1;
	}
}
