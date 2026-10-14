<?php

namespace tests\unit\mocks\repositories;

use app\domain\course\LessonBlock;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\models\dtos\LessonBlockDatabase;
use app\models\mappers\LessonBlockMapper;

/**
 * Shared mock repository for LessonBlock tests.
 *
 * Simulates raw database storage and validates DTO/Mapper transformations.
 */
class MockLessonBlockRepository implements LessonBlockRepositoryInterface
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
	 * Find a block by ID.
	 *
	 * @param int $id
	 * @return LessonBlock|null
	 */
	public function find_by_id(int $id): ?LessonBlock
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return LessonBlockMapper::to_entity(new LessonBlockDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find all non-deleted blocks for a lesson ordered by sort_order ASC.
	 *
	 * @param int $lesson_id
	 * @return array<LessonBlock>
	 */
	public function find_by_lesson_id(int $lesson_id): array
	{
		$matched = [];
		foreach ($this->rows as $row) {
			if ($row['lesson_id'] === $lesson_id && empty($row['deleted_at'])) {
				$matched[] = $row;
			}
		}

		usort($matched, static function (array $item_a, array $item_b): int {
			return $item_a['sort_order'] <=> $item_b['sort_order'];
		});

		return LessonBlockMapper::to_entities($matched);
	}

	/**
	 * Create a new lesson block.
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function create(LessonBlock $block): LessonBlock
	{
		$data = LessonBlockMapper::to_database_create($block);
		$insert_id = $this->auto_increment++;

		$data['id'] = $insert_id;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['deleted_at'] = null;

		$child_data = LessonBlockMapper::to_child_database_data($block, $insert_id);
		if ($child_data !== null) {
			$data = array_merge($data, $child_data['data']);
		}

		$this->rows[$insert_id] = $data;

		return LessonBlockMapper::to_entity(new LessonBlockDatabase($data));
	}

	/**
	 * Save (insert or update) a lesson block.
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function save(LessonBlock $block): LessonBlock
	{
		if ($block->get_id() === null) {
			return $this->create($block);
		}

		$existing_id = $block->get_id();
		$data = LessonBlockMapper::to_database_update($block);

		$child_data = LessonBlockMapper::to_child_database_data($block, $existing_id);
		if ($child_data !== null) {
			$data = array_merge($data, $child_data['data']);
		}

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

		return LessonBlockMapper::to_entity(new LessonBlockDatabase($this->rows[$existing_id]));
	}

	/**
	 * Delete a lesson block (soft delete).
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function delete(LessonBlock $block): LessonBlock
	{
		$block->delete();
		return $this->save($block);
	}

	/**
	 * Reorder blocks for a lesson.
	 *
	 * @param int $lesson_id
	 * @param array<int> $block_ids
	 * @return void
	 */
	public function reorder(int $lesson_id, array $block_ids): void
	{
		$position = 1;
		foreach ($block_ids as $block_id) {
			if (isset($this->rows[$block_id]) && $this->rows[$block_id]['lesson_id'] === $lesson_id) {
				$this->rows[$block_id]['sort_order'] = $position;
				$position++;
			}
		}
	}

	/**
	 * Get next sort order value for a lesson.
	 *
	 * @param int $lesson_id
	 * @return int
	 */
	public function get_next_sort_order(int $lesson_id): int
	{
		$max_order = 0;
		foreach ($this->rows as $row) {
			if ($row['lesson_id'] === $lesson_id && empty($row['deleted_at'])) {
				if ((int) $row['sort_order'] > $max_order) {
					$max_order = (int) $row['sort_order'];
				}
			}
		}
		return $max_order + 1;
	}
}
