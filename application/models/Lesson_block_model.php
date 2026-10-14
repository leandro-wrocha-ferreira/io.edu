<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\LessonBlock;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\models\dtos\LessonBlockDatabase;
use app\models\mappers\LessonBlockMapper;

/**
 * Lesson block model implementing LessonBlockRepositoryInterface.
 */
class Lesson_block_model extends MY_Model implements LessonBlockRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'lesson_blocks';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Build base select query with child tables joined.
	 *
	 * @return void
	 */
	private function set_block_select(): void
	{
		$this->db
			->select('lesson_blocks.*,
				vb.provider, vb.url, vb.duration_in_seconds,
				tb.content,
				ab.filename, ab.filepath, ab.file_size, ab.file_type,
				COALESCE(tb.min_reading_time_in_minutes, ab.min_reading_time_in_minutes, 0) AS min_reading_time_in_minutes')
			->from($this->table)
			->join('video_blocks vb', 'vb.block_id = lesson_blocks.id', 'left')
			->join('text_blocks tb', 'tb.block_id = lesson_blocks.id', 'left')
			->join('attachment_blocks ab', 'ab.block_id = lesson_blocks.id', 'left');
	}

	/**
	 * Find a lesson block by ID.
	 *
	 * @param int $id Block ID
	 * @return LessonBlock|null
	 */
	public function find_by_id(int $id): ?LessonBlock
	{
		$this->set_block_select();
		$row = $this->db
			->where('lesson_blocks.id', $id)
			->where('lesson_blocks.deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return LessonBlockMapper::to_entity(new LessonBlockDatabase($row));
	}

	/**
	 * Find all non-deleted blocks for a lesson ordered by sort_order ASC.
	 *
	 * @param int $lesson_id
	 * @return array<LessonBlock>
	 */
	public function find_by_lesson_id(int $lesson_id): array
	{
		$this->set_block_select();
		$rows = $this->db
			->where('lesson_blocks.lesson_id', $lesson_id)
			->where('lesson_blocks.deleted_at IS NULL', null, false)
			->order_by('lesson_blocks.sort_order', 'ASC')
			->order_by('lesson_blocks.id', 'ASC')
			->get()
			->result_array();

		return LessonBlockMapper::to_entities($rows);
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
		$insert_id = (int) $this->insert($data);

		$child_data = LessonBlockMapper::to_child_database_data($block, $insert_id);
		if ($child_data !== null) {
			$this->db->insert($child_data['table'], $child_data['data']);
		}

		$created = $this->find_by_id($insert_id);
		return $created ?? $block;
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

		$data = LessonBlockMapper::to_database_update($block);
		$this->update($data, ['id' => $block->get_id()]);

		$child_data = LessonBlockMapper::to_child_database_data($block, $block->get_id());
		if ($child_data !== null) {
			$child_exists = $this->db
				->where('block_id', $block->get_id())
				->count_all_results($child_data['table']) > 0;

			if ($child_exists) {
				$this->db
					->where('block_id', $block->get_id())
					->update($child_data['table'], $child_data['data']);
			} else {
				$this->db->insert($child_data['table'], $child_data['data']);
			}
		}

		$updated = $this->find_by_id($block->get_id());
		return $updated ?? $block;
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
	 * Reorder blocks for a given lesson.
	 *
	 * @param int $lesson_id
	 * @param array<int> $block_ids
	 * @return void
	 */
	public function reorder(int $lesson_id, array $block_ids): void
	{
		if (empty($block_ids)) {
			return;
		}

		$position = 1;
		foreach ($block_ids as $block_id) {
			$this->db
				->where('id', (int) $block_id)
				->where('lesson_id', $lesson_id)
				->update($this->table, ['sort_order' => $position]);
			$position++;
		}
	}

	/**
	 * Get next sort order value for a new block in a lesson.
	 *
	 * @param int $lesson_id
	 * @return int
	 */
	public function get_next_sort_order(int $lesson_id): int
	{
		$row = $this->db
			->select_max('sort_order', 'max_order')
			->where('lesson_id', $lesson_id)
			->where('deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		return isset($row['max_order']) ? ((int) $row['max_order']) + 1 : 1;
	}
}
