<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class_lesson model representing the junction table between classes and lessons.
 */
class Class_lesson_model extends MY_Model
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'class_lessons';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get all lesson IDs associated with a class.
	 *
	 * @param int $class_id
	 * @return int[]
	 */
	public function get_lesson_ids_by_class(int $class_id): array
	{
		$rows = $this->db
			->select('lesson_id')
			->from($this->table)
			->where('class_id', $class_id)
			->get()
			->result_array();

		return array_map(static fn($r) => (int) $r['lesson_id'], $rows);
	}
}
