<?php

use app\domain\exceptions\NotFoundException;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model for the Application.
 *
 * Provides a lightweight CRUD Engine returning and receiving 100% pure data
 * (raw arrays and scalars) with automatic auditing via the `logs` table.
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
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Fetch all records as raw row arrays.
	 *
	 * Can be chained with previous $this->db calls.
	 *
	 * @return array<int, array<string, mixed>> List of raw row arrays
	 */
	public function find_all(): array
	{
		if ($this->table === '') {
			return [];
		}

		return $this->db->from($this->table)
			->get()
			->result_array();
	}

	/**
	 * Fetch a single record by primary key ID as a raw array.
	 *
	 * @param int $id Primary key ID
	 * @return array<string, mixed>|null Record raw row array, or null if not found
	 */
	public function find_by_id(int $id)
	{
		if ($this->table === '') {
			return null;
		}

		$row = $this->db->where($this->primary_key, $id)
			->get($this->table)
			->row_array();

		if (empty($row)) {
			return null;
		}

		return $row;
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

		return $this->db->from($this->table)
			->count_all_results();
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param array<string, mixed> $data Column => value map
	 * @param string|null $table Optional table name override
	 * @return int|string|null Inserted ID or null if table is not set
	 */
	public function insert(array $data, ?string $table = null): int|string|null
	{
		$target_table = $table ?? $this->table;

		if ($target_table === '') {
			return null;
		}

		$this->db->insert($target_table, $data);
		$insert_id = $this->db->insert_id();

		if ($insert_id === false) return null;

		$this->log_audit('insert', $insert_id, null, $data, $target_table);

		return $insert_id;
	}

	/**
	 * Insert multiple records in a batch.
	 *
	 * @param array<int, array<string, mixed>> $data Array of column => value maps
	 * @param string|null $table Optional table name override
	 * @return int|null Number of inserted rows
	 */
	public function insert_many(array $data, ?string $table = null): int|null
	{
		$target_table = $table ?? $this->table;

		if ($target_table === '') return null;

		$inserted_count = $this->db->insert_batch($target_table, $data);
		$this->log_audit('insert_many', 'batch', null, $data, $target_table);
		return $inserted_count;
	}

	/**
	 * Update a single record matching specified conditions.
	 *
	 * @param array<string, mixed> $data Data to update
	 * @param array<string, mixed> $where Filter conditions. Usually the primary key (e.g. ['id' => $id])
	 * @param string|null $table Optional table name override
	 * @return bool
	 */
	public function update(array $data, array $where, ?string $table = null): bool
	{
		$target_table = $table ?? $this->table;

		$this->db->where($where);

		// Build the SELECT query string without resetting the Query Builder state
		$sql = $this->db->get_compiled_select($target_table, FALSE);
		// Execute the raw query to get the 'before' state of the single record
		$before = $this->db->query($sql)->row_array();

		// Execute the UPDATE which will consume the Query Builder state
		$result = $this->db->update($target_table, $data);

		if ($result) {
			$row_identifier = $before[$this->primary_key] ?? null;
			$this->log_audit('update', $row_identifier, $before, $data, $target_table);
		}

		return $result;
	}

	/**
	 * Update multiple records.
	 *
	 * Supports both:
	 * 1. Batch updating different values per row: update_many([['id' => 1, 'val' => 'a'], ...], 'id')
	 * 2. Mass updating same values matching conditions: update_many(['status' => 'active'], ['role_id' => 2])
	 *
	 * @param array $data List of row arrays OR single column-value map
	 * @param string|array $where_or_index Index column name for batch OR filter conditions array
	 * @param string|null $table Optional table name override
	 * @return int Number of affected rows
	 */
	public function update_many(array $data, string|array $where_or_index = 'id', ?string $table = null): int
	{
		if (empty($data)) {
			return 0;
		}

		$target_table = $table ?? $this->table;

		if (is_array($where_or_index)) {
			$this->db->where($where_or_index);
			$sql = $this->db->get_compiled_select($target_table, FALSE);
			$before = $this->db->query($sql)->result_array();

			if (empty($before)) {
				$this->db->reset_query();
				return 0;
			}

			$this->db->update($target_table, $data);
			$affected = $this->db->affected_rows();

			if ($affected > 0) {
				$this->log_audit('update_many', 'batch', $before, $data, $target_table);
			}

			return $affected;
		}

		$affected = $this->db->update_batch($target_table, $data, $where_or_index);
		$this->log_audit('update_many', 'batch', null, $data, $target_table);

		return (int) $affected;
	}

	/**
	 * Destroy a single record matching specified conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions. Usually the primary key (e.g. ['id' => $id])
	 * @param string|null $table Optional table name override
	 * @return bool
	 */
	public function destroy(array $where, ?string $table = null): bool
	{
		$target_table = $table ?? $this->table;

		$this->db->where($where);

		// Build the SELECT query string without resetting the Query Builder state
		$sql = $this->db->get_compiled_select($target_table, FALSE);
		$before = $this->db->query($sql)->row_array();

		// Execute the DELETE which will consume the Query Builder state
		$result = $this->db->delete($target_table);

		if ($result) {
			$row_identifier = $before[$this->primary_key] ?? null;
			$this->log_audit('destroy', $row_identifier, $before, null, $target_table);
		}

		return $result;
	}

	/**
	 * Destroy multiple records matching specified conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions (e.g. ['role_id' => $id])
	 * @param string|null $table Optional table name override
	 * @return int Number of affected rows
	 */
	public function destroy_many(array $where, ?string $table = null): int
	{
		$target_table = $table ?? $this->table;

		$this->db->where($where);

		$sql = $this->db->get_compiled_select($target_table, FALSE);
		$before = $this->db->query($sql)->result_array();

		if (empty($before)) {
			$this->db->reset_query();
			return 0;
		}

		$this->db->delete($target_table);
		$affected = $this->db->affected_rows();

		if ($affected > 0) {
			$this->log_audit('destroy_many', 'batch', $before, null, $target_table);
		}

		return $affected;
	}

	/**
	 * Audit log for successful operations into the `logs` table.
	 *
	 * @param string $action Database action context
	 * @param int|string|null $row_identifier The affected row identifier or conditions
	 * @param array|null $before Data before the operation
	 * @param array|null $after Data after the operation
	 * @param string|null $table Optional table name override
	 * @return void
	 */
	protected function log_audit(string $action, int|string|null $row_identifier, ?array $before, ?array $after, ?string $table = null): void
	{
		$target_table = $table ?? $this->table;

		if ($target_table === 'logs' || empty($target_table)) {
			return;
		}

		$user_id = $this->session->userdata('user_id') ?? null;

		if ($action === 'update' && is_array($before) && is_array($after)) {
			$diff_before = [];
			$diff_after = [];

			$is_multi_row = array_is_list($before) && isset($before[0]) && is_array($before[0]);
			$rows_before = $is_multi_row ? $before : [$before];

			foreach ($rows_before as $row) {
				$row_diff_before = [];
				$row_diff_after = [];

				foreach ($after as $key => $value) {
					if (is_array($row) && array_key_exists($key, $row)) {
						if ($row[$key] != $value) {
							$row_diff_before[$key] = $row[$key];
							$row_diff_after[$key] = $value;
						}
					} else {
						$row_diff_after[$key] = $value;
					}
				}

				if ($is_multi_row) {
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
			'after' => $after,
		], JSON_UNESCAPED_UNICODE);

		$this->db->insert('logs', [
			'user_execute' => $user_id,
			'table' => $target_table,
			'row' => $row_identifier,
			'content' => $content,
		]);
	}
}
