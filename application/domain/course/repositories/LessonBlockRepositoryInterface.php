<?php

namespace app\domain\course\repositories;

use app\domain\course\LessonBlock;

/**
 * Repository interface for LessonBlock persistence.
 */
interface LessonBlockRepositoryInterface
{
	/**
	 * Find a lesson block by ID.
	 *
	 * @param int $id Block ID
	 * @return LessonBlock|null
	 */
	public function find_by_id(int $id): ?LessonBlock;

	/**
	 * Find all non-deleted blocks for a lesson ordered by sort_order ASC.
	 *
	 * @param int $lesson_id
	 * @return array<LessonBlock>
	 */
	public function find_by_lesson_id(int $lesson_id): array;

	/**
	 * Create a new lesson block.
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function create(LessonBlock $block): LessonBlock;

	/**
	 * Save (insert or update) a lesson block.
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function save(LessonBlock $block): LessonBlock;

	/**
	 * Delete a lesson block (soft delete).
	 *
	 * @param LessonBlock $block
	 * @return LessonBlock
	 */
	public function delete(LessonBlock $block): LessonBlock;

	/**
	 * Reorder blocks for a given lesson.
	 *
	 * @param int $lesson_id
	 * @param array<int> $block_ids
	 * @return void
	 */
	public function reorder(int $lesson_id, array $block_ids): void;

	/**
	 * Get the next sequential sort order for a new block in a lesson.
	 *
	 * @param int $lesson_id
	 * @return int
	 */
	public function get_next_sort_order(int $lesson_id): int;
}
