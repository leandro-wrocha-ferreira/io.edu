<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\Lesson;

/**
 * Lesson Model for curriculum lessons.
 */
class Lesson_model extends MY_Model
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'lessons';

	/**
	 * Entity class.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Lesson::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get all lessons for a section ordered by sort_order.
	 *
	 * @param int $section_id
	 * @return Lesson[]
	 */
	public function find_by_section(int $section_id): array
	{
		$rows = $this->db
			->from($this->table)
			->where('section_id', $section_id)
			->order_by('sort_order', 'ASC')
			->get()
			->result_array();

		return $this->to_entities($rows);
	}
}
