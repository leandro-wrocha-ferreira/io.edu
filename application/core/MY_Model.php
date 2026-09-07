<?php

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
		if (empty($rows) || empty($this->entity_class) || !method_exists($this->entity_class, 'from_database')) {
			return $rows;
		}

		$class = $this->entity_class;
		return array_map(static function (array $row) use ($class) {
			return $class::from_database($row);
		}, $rows);
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
		if ($this->table !== '') {
			$this->db->from($this->table);
		}
		$rows = $this->db->get()->result_array();
		return $this->to_entities($rows);
	}

	/**
	 * Fetch a single record by primary key ID and hydrate as Domain Entity if $entity_class is set.
	 *
	 * @param int|string $id Primary key ID
	 * @return object|array|null Record entity, raw row array, or null if not found
	 */
	public function find_by_id($id)
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
		if ($this->table !== '') {
			$this->db->from($this->table);
		}
		return $this->db->count_all_results();
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param array $data Column => value map
	 * @return int|string Inserted ID
	 */
	public function insert(array $data)
	{
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
		return (int) $inserted_count;
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
		
		// Execute the UPDATE which will consume the Query Builder state
		$result = $this->db->update($this->table, $data);
		
		if ($result) {
			$row_identifier = isset($where[$this->primary_key]) ? $where[$this->primary_key] : json_encode($where);
			$this->log_audit('update', $row_identifier, $before, $data);
		}
		
		return (bool) $result;
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
		
		// Execute the DELETE which will consume the Query Builder state
		$result = $this->db->delete($this->table);
		
		if ($result) {
			$row_identifier = isset($where[$this->primary_key]) ? $where[$this->primary_key] : json_encode($where);
			$this->log_audit('delete', $row_identifier, $before, null);
		}
		
		return (bool) $result;
	}

	/**
	 * Audit log for successful operations into the `logs` table.
	 *
	 * @param string $action Database action context
	 * @param mixed $row_identifier The affected row identifier or conditions
	 * @param mixed $before Data before the operation
	 * @param mixed $after Data after the operation
	 * @return void
	 */
	protected function log_audit(string $action, $row_identifier, $before = null, $after = null): void
	{
		if ($this->table === 'logs' || empty($this->table)) {
			return;
		}

		$user_id = null;
		if (isset($this->session) && $this->session->userdata('user_id')) {
			$user_id = $this->session->userdata('user_id');
		}

		$content = json_encode([
			'action' => $action,
			'before' => $before,
			'after'  => $after
		], JSON_UNESCAPED_UNICODE);

		$row_str = is_array($row_identifier) ? json_encode($row_identifier) : (string) $row_identifier;

		$log_data = [
			'user_execute' => $user_id,
			'table' => $this->table,
			'row' => $row_str,
			'content' => $content
		];

		$this->db->insert('logs', $log_data);
	}
}
