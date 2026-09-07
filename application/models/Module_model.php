<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\Module;

/**
 * Module Model for managing course curriculum modules.
 */
class Module_model extends MY_Model
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'modules';

	/**
	 * Entity class.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Module::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get all modules for a course ordered by sort_order.
	 *
	 * @param int $course_id
	 * @return Module[]
	 */
	public function find_by_course(int $course_id): array
	{
		$rows = $this->db
			->from($this->table)
			->where('course_id', $course_id)
			->order_by('sort_order', 'ASC')
			->get()
			->result_array();

		return $this->to_entities($rows);
	}
}
