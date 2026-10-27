<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;
use app\models\dtos\CostCenterDatabase;
use app\models\mappers\CostCenterMapper;

/**
 * Cost center model implementing CostCenterRepositoryInterface.
 */
class Cost_center_model extends MY_Model implements CostCenterRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'cost_centers';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a cost center by its ID.
	 *
	 * @param int $id
	 * @return CostCenter|null
	 */
	public function find_by_id(int $id): ?CostCenter
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->where('deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return CostCenterMapper::to_entity(new CostCenterDatabase($row));
	}


	/**
	 * Find all active cost centers.
	 *
	 * @return array<CostCenter>
	 */
	public function find_all_active(): array
	{
		$rows = $this->db->from($this->table)
			->where('is_active', 1)
			->where('deleted_at IS NULL', null, false)
			->order_by('name', 'ASC')
			->get()
			->result_array();

		return CostCenterMapper::to_entities($rows);
	}

	/**
	 * Create a new cost center record.
	 *
	 * @param CostCenter $cost_center
	 * @return CostCenter
	 */
	public function create(CostCenter $cost_center): CostCenter
	{
		$data = CostCenterMapper::to_database_create($cost_center);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $cost_center;
	}

	/**
	 * Save a cost center.
	 *
	 * @param CostCenter $cost_center
	 * @return CostCenter
	 */
	public function save(CostCenter $cost_center): CostCenter
	{
		if ($cost_center->get_id() === null) {
			return $this->create($cost_center);
		}

		$data = CostCenterMapper::to_database_update($cost_center);
		$this->update($data, [$this->primary_key => $cost_center->get_id()]);

		return $this->find_by_id($cost_center->get_id()) ?? $cost_center;
	}

	/**
	 * Find cost centers with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Order column
	 * @param string $order_dir Order direction
	 * @param int|null $is_active Optional status filter
	 * @return array{data: array<CostCenter>, recordsFiltered: int}
	 */
	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $is_active = null
	): array
	{
		$total_filtered = $this->_count_paginated($search, $is_active);

		$allowed = ['id', 'name', 'is_active', 'created_at'];
		$column = in_array($order_col, $allowed, true) ? $order_col : 'name';
		$direction = strtoupper($order_dir) === 'DESC' ? 'DESC' : 'ASC';

		$this->db
			->where('deleted_at IS NULL', null, false)
			->order_by($column, $direction)
			->limit($length, $start);

		if ($is_active !== null) {
			$this->db->where('is_active', $is_active);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('name', $search)
				->or_like('description', $search);
			if (is_numeric($search)) {
				$this->db->or_where('id', (int) $search);
			}
			$this->db->group_end();
		}

		$rows = $this->db->get($this->table)->result_array();

		return [
			'data' => CostCenterMapper::to_entities($rows),
			'recordsFiltered' => $total_filtered,
		];
	}

	/**
	 * Count filtered cost centers.
	 *
	 * @param string $search Search query
	 * @param int|null $is_active Status filter
	 * @return int
	 */
	private function _count_paginated(string $search, ?int $is_active = null): int
	{
		$this->db->where('deleted_at IS NULL', null, false);

		if ($is_active !== null) {
			$this->db->where('is_active', $is_active);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('name', $search)
				->or_like('description', $search);
			if (is_numeric($search)) {
				$this->db->or_where('id', (int) $search);
			}
			$this->db->group_end();
		}

		return (int) $this->db->from($this->table)->count_all_results();
	}

	/**
	 * Count total non-deleted cost centers.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		return (int) $this->db
			->where('deleted_at IS NULL', null, false)
			->from($this->table)
			->count_all_results();
	}

	/**
	 * Soft delete a cost center.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool
	{
		return (bool) $this->update([
			'deleted_at' => date('Y-m-d H:i:s'),
		], ['id' => $id]);
	}

	/**
	 * Check if cost center is referenced by any product.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function has_linked_products(int $id): bool
	{
		$count = $this->db->from('products')
			->where('cost_center_id', $id)
			->where('deleted_at IS NULL', null, false)
			->count_all_results();

		return $count > 0;
	}
}
