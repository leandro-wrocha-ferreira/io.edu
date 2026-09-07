<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\Section;

/**
 * Section Model for managing curriculum sections inside modules.
 */
class Section_model extends MY_Model
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'sections';

	/**
	 * Entity class.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Section::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get all sections for a module ordered by sort_order.
	 *
	 * @param int $module_id
	 * @return Section[]
	 */
	public function find_by_module(int $module_id): array
	{
		$rows = $this->db
			->from($this->table)
			->where('module_id', $module_id)
			->order_by('sort_order', 'ASC')
			->get()
			->result_array();

		return $this->to_entities($rows);
	}
}
