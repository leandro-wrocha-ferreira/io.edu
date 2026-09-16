<?php

use app\domain\exceptions\NotFoundException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model for the Application.
 *
 * Provides a lightweight CRUD Engine with entity hydration and 
 * automatic auditing via the `logs` table.
 */
class MY_Model extends CI_Model
{
	/**
	 * Database table name.
	 *
	 * @var string
	 */
	protected string $table = '';

	/**
	 * Primary key column name.
	 *
	 * @var string
	 */
	protected string $primary_key = 'id';

	/**
	 * Whether the table uses soft deletes.
	 *
	 * @var bool
	 */
	protected bool $soft_delete = false;

	/**
	 * Target Domain Entity class FQCN for automatic row hydration (optional).
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Hydrate a single database row array into a Domain Entity if $entity_class is configured.
	 *
	 * @param array|null $row Raw database row array
	 * @return object|array|null Domain Entity instance, raw array, or null
	 */
	protected function to_entity(?array $row)
	{
		if ($row === null) {
			return null;
		}

		if (!empty($this->entity_class) && method_exists($this->entity_class, 'from_database')) {
			$class = $this->entity_class;
			return $class::from_database($row);
		}

		return $row;
	}

	/**
	 * Hydrate multiple database row arrays into Domain Entities if $entity_class is configured.
	 *
	 * @param array $rows List of raw database row arrays
	 * @return array List of Domain Entities or raw arrays
	 */
	protected function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return $rows;
		}

		if (!empty($this->entity_class) && method_exists($this->entity_class, 'from_database')) {
			$class = $this->entity_class;
			return array_map(static function (array $row) use ($class) {
				return $class::from_database($row);
			}, $rows);
		}

		return $rows;
	}

	/**
	 * Fetch all records hydrated as Domain Entities if $entity_class is set.
	 *
	 * Can be chained with previous $this->db calls.
	 *
	 * @return array List of Domain Entities or raw row arrays
	 */
	public function find_all(): array
	{
		if ($this->table === '') {
			return [];
		}

		$rows = $this->db->from($this->table)
			->get()
			->result_array();
		
		return $this->to_entities($rows);
	}

	/**
	 * Fetch a single record by primary key ID and hydrate as Domain Entity if $entity_class is set.
	 *
	 * @param int|string $id Primary key ID
	 * @return object|array|null Record entity, raw row array, or null if not found
	 */
	public function find_by_id(int|string $id)
	{
		if ($this->table === '') {
			return null;
		}

		$pk = (strpos($this->primary_key, '.') === false)
			? $this->table . '.' . $this->primary_key
			: $this->primary_key;

		$row = $this->db->where($pk, $id)->get($this->table)->row_array() ?: null;
		
		return $this->to_entity($row);
	}

	/**
	 * Count all records in the table or matching active query builder conditions.
	 *
	 * @return int Total record count
	 */
	public function count_all(): int
	{
		if ($this->table === '') {
			return 0;
		}

		$count = $this->db->from($this->table)
			->count_all_results();
		
		return $count;
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param array $data Column => value map
	 * @return int|string Inserted ID
	 */
	public function insert(array $data)
	{
		if ($this->table === '') {
			return null;
		}

		$this->db->insert($this->table, $data);
		$insert_id = $this->db->insert_id();
		
		$this->log_audit('insert', $insert_id, null, $data);
		return $insert_id;
	}

	/**
	 * Insert multiple records in a batch.
	 *
	 * @param array $data Array of column => value maps
	 * @return int Number of inserted rows
	 */
	public function insert_batch(array $data): int
	{
		$inserted_count = $this->db->insert_batch($this->table, $data);
		$this->log_audit('insert_batch', 'batch', null, $data);
		return $inserted_count;
	}

	/**
	 * Update records matching specified conditions.
	 *
	 * @param array $data Data to update
	 * @param array $where Filter conditions. Usually the primary key (e.g. ['id' => $id])
	 * @return bool
	 */
	public function update(array $data, array $where): bool
	{
		$this->db->where($where);
		
		// Build the SELECT query string without resetting the Query Builder state
		$sql = $this->db->get_compiled_select($this->table, FALSE);
		// Execute the raw query to get the 'before' state
		$before = $this->db->query($sql)->result_array();

		if (empty($before)) {
			throw new NotFoundException();
		}
		
		// Execute the UPDATE which will consume the Query Builder state
		$result = $this->db->update($this->table, $data);
		
		if ($result) {
			$row_identifier = $before[0][$this->primary_key];
			if (count($before) > 1) {
				$row_identifier = 'batch';
			}

			$this->log_audit('update', $row_identifier, $before, $data);
		}
		
		return $result;
	}

	/**
	 * Delete records matching specified conditions.
	 *
	 * @param array $where Filter conditions. Usually the primary key (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		$this->db->where($where);
		
		// Build the SELECT query string without resetting the Query Builder state
		$sql = $this->db->get_compiled_select($this->table, FALSE);
		$before = $this->db->query($sql)->result_array();

		if (empty($before)) {
			throw new NotFoundException();
		}
		
		// Execute the DELETE which will consume the Query Builder state
		$result = $this->db->delete($this->table);
		
		if ($result) {
			$row_identifier = $before[0][$this->primary_key];
			if (count($before) > 1) {
				$row_identifier = 'batch';
			}

			$this->log_audit('delete', $row_identifier, $before, null);
		}
		
		return $result;
	}

	/**
	 * Audit log for successful operations into the `logs` table.
	 *
	 * @param string $action Database action context
	 * @param int|string $row_identifier The affected row identifier or conditions
	 * @param array|null $before Data before the operation
	 * @param array|null $after Data after the operation
	 * @return void
	 */
	protected function log_audit(string $action, int|string $row_identifier, ?array $before, ?array $after): void
	{
		if ($this->table === 'logs' || empty($this->table)) {
			return;
		}

		$user_id = $this->session->userdata('user_id') ?? null;

		if ($action === 'update' && is_array($before) && is_array($after)) {
			$diff_before = [];
			$diff_after = [];

			$is_batch = count($before) > 1;

			foreach ($before as $row) {
				$row_diff_before = [];
				$row_diff_after = [];

				foreach ($after as $key => $value) {
					if (array_key_exists($key, $row)) {
						if ($row[$key] != $value) {
							$row_diff_before[$key] = $row[$key];
							$row_diff_after[$key] = $value;
						}
					} else {
						$row_diff_after[$key] = $value;
					}
				}

				if ($is_batch) {
					$diff_before[] = $row_diff_before;
					$diff_after[] = $row_diff_after;
				} else {
					$diff_before = $row_diff_before;
					$diff_after = $row_diff_after;
				}
			}
	
			$before = $diff_before;
			$after = $diff_after;
		}

		$content = json_encode([
			'action' => $action,
			'before' => $before,
			'after' => $after
		], JSON_UNESCAPED_UNICODE);

		$this->db->insert('logs', [
			'user_execute' => $user_id,
			'table' => $this->table,
			'row' => $row_identifier,
			'content' => $content
		]);
	}
}
