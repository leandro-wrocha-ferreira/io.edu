<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Model for the Application.
 *
 * Extends CI_Model to provide a lightweight Lifecycle CRUD Engine with
 * automatic global query scopes, before/after event callbacks, and centralized error logging.
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
	 * Callbacks executed automatically before a SELECT / GET query is run.
	 *
	 * @var array
	 */
	protected array $before_get = [];

	/**
	 * Callbacks executed after a SELECT / GET query has run.
	 *
	 * @var array
	 */
	protected array $after_get = [];

	/**
	 * Callbacks executed before an INSERT operation.
	 *
	 * @var array
	 */
	protected array $before_create = [];

	/**
	 * Callbacks executed after an INSERT operation.
	 *
	 * @var array
	 */
	protected array $after_create = [];

	/**
	 * Callbacks executed before an UPDATE operation.
	 *
	 * @var array
	 */
	protected array $before_update = [];

	/**
	 * Callbacks executed after an UPDATE operation.
	 *
	 * @var array
	 */
	protected array $after_update = [];

	/**
	 * Callbacks executed before a DELETE operation.
	 *
	 * @var array
	 */
	protected array $before_delete = [];

	/**
	 * Callbacks executed after a DELETE operation.
	 *
	 * @var array
	 */
	protected array $after_delete = [];

	/**
	 * Scopes temporarily disabled for the next query.
	 *
	 * @var array
	 */
	protected array $disabled_scopes = [];

	/**
	 * Whether all global query scopes are disabled for the next query.
	 *
	 * @var bool
	 */
	protected bool $disable_all_scopes = false;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Temporarily disable a specific global query scope for the next query.
	 *
	 * @param string $scope_name Name of the scope method (e.g. 'scope_exclude_admin_master')
	 * @return self
	 */
	public function without_scope(string $scope_name): self
	{
		$this->disabled_scopes[] = $scope_name;
		return $this;
	}

	/**
	 * Temporarily disable all global query scopes for the next query.
	 *
	 * @return self
	 */
	public function without_global_scopes(): self
	{
		$this->disable_all_scopes = true;
		return $this;
	}

	/**
	 * Re-enable all global query scopes.
	 *
	 * @return self
	 */
	public function with_global_scopes(): self
	{
		$this->disable_all_scopes = false;
		$this->disabled_scopes = [];
		return $this;
	}

	/**
	 * Fetch all records from the table.
	 *
	 * Applies before_get scopes and executes query. Can be chained with previous $this->db calls.
	 *
	 * @return array List of rows
	 */
	public function get_all(): array
	{
		return $this->_run_pipeline('get', function () {
			if ($this->table !== '') {
				$this->db->from($this->table);
			}
			return $this->db->get()->result_array();
		});
	}

	/**
	 * Alias for get_all() for convenience.
	 *
	 * @return array
	 */
	public function find_all(): array
	{
		return $this->get_all();
	}

	/**
	 * Fetch a single record by primary key ID.
	 *
	 * @param int|string $id Primary key ID
	 * @return array|null Record row or null if not found
	 */
	public function get_by_id($id): ?array
	{
		return $this->_run_pipeline('get', function () use ($id) {
			if ($this->table !== '') {
				$this->db->from($this->table);
			}
			$pk = (strpos($this->primary_key, '.') === false && $this->table !== '')
				? $this->table . '.' . $this->primary_key
				: $this->primary_key;

			return $this->db->where($pk, $id)->get()->row_array() ?: null;
		}, [], $id);
	}

	/**
	 * Alias for get_by_id().
	 *
	 * @param int|string $id
	 * @return array|null
	 */
	public function find_by_id($id): ?array
	{
		return $this->get_by_id($id);
	}

	/**
	 * Fetch a single record matching given conditions.
	 *
	 * @param array $where Associative array of column => value filters
	 * @return array|null Record row or null if not found
	 */
	public function get_by(array $where): ?array
	{
		return $this->_run_pipeline('get', function () use ($where) {
			if ($this->table !== '') {
				$this->db->from($this->table);
			}
			return $this->db->where($where)->get()->row_array() ?: null;
		}, $where);
	}

	/**
	 * Count all records in the table.
	 *
	 * Applies before_get scopes and counts results.
	 *
	 * @return int Total record count
	 */
	public function count_all(): int
	{
		return (int) $this->_run_pipeline('get', function () {
			if ($this->table !== '') {
				$this->db->from($this->table);
			}
			return $this->db->count_all_results();
		});
	}

	/**
	 * Count records matching specified conditions.
	 *
	 * @param array $where Associative array of column => value filters
	 * @return int
	 */
	public function count_by(array $where): int
	{
		return (int) $this->_run_pipeline('get', function () use ($where) {
			if ($this->table !== '') {
				$this->db->from($this->table);
			}
			return $this->db->where($where)->count_all_results();
		}, $where);
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param array $data Column => value map
	 * @return int|string Inserted ID
	 */
	public function insert(array $data)
	{
		return $this->_run_pipeline('create', function () use ($data) {
			$this->db->insert($this->table, $data);
			return $this->db->insert_id();
		}, $data);
	}

	/**
	 * Insert multiple records in a batch.
	 *
	 * @param array $data Array of column => value maps
	 * @return int Number of inserted rows
	 */
	public function insert_batch(array $data): int
	{
		return (int) $this->_run_pipeline('create', function () use ($data) {
			return $this->db->insert_batch($this->table, $data);
		}, $data);
	}

	/**
	 * Update a record by primary key ID.
	 *
	 * @param int|string $id Primary key value
	 * @param array $data Data to update
	 * @return bool
	 */
	public function update_record($id, array $data): bool
	{
		return (bool) $this->_run_pipeline('update', function () use ($id, $data) {
			return $this->db->where($this->primary_key, $id)->update($this->table, $data);
		}, $data, $id);
	}

	/**
	 * Update records matching specified conditions.
	 *
	 * @param array $where Filter conditions
	 * @param array $data Data to update
	 * @return bool
	 */
	public function update_by(array $where, array $data): bool
	{
		return (bool) $this->_run_pipeline('update', function () use ($where, $data) {
			return $this->db->where($where)->update($this->table, $data);
		}, $data, $where);
	}

	/**
	 * Delete a record by primary key ID.
	 *
	 * @param int|string $id Primary key ID
	 * @return bool
	 */
	public function delete_record($id): bool
	{
		return (bool) $this->_run_pipeline('delete', function () use ($id) {
			return $this->db->where($this->primary_key, $id)->delete($this->table);
		}, [], $id);
	}

	/**
	 * Delete records matching specified conditions.
	 *
	 * @param array $where Filter conditions
	 * @return bool
	 */
	public function delete_by(array $where): bool
	{
		return (bool) $this->_run_pipeline('delete', function () use ($where) {
			return $this->db->where($where)->delete($this->table);
		}, [], $where);
	}

	/**
	 * Execute the lifecycle pipeline for a given action.
	 *
	 * @param string $action Action name ('get', 'create', 'update', 'delete')
	 * @param callable $query_fn Query callback
	 * @param array $data Payload data
	 * @param mixed ...$args Additional callback parameters
	 * @return mixed Result of $query_fn
	 * @throws \Throwable
	 */
	protected function _run_pipeline(string $action, callable $query_fn, array $data = [], ...$args)
	{
		try {
			// 1. Run before callbacks (e.g. global scopes before_get, validations before_create)
			$this->_trigger('before_' . $action, $data, ...$args);

			// 2. Execute query callable
			$result = $query_fn();

			// 3. Run after callbacks (e.g. after_create audit logs, after_get transformations)
			$this->_trigger('after_' . $action, $result, $data, ...$args);

			return $result;
		} catch (\Throwable $e) {
			$this->log_database_failure($e, $action);
			throw $e;
		} finally {
			// Reset temporary scope overrides after every query
			if ($action === 'get') {
				$this->disabled_scopes = [];
				$this->disable_all_scopes = false;
			}
		}
	}

	/**
	 * Trigger registered lifecycle callbacks for an event.
	 *
	 * @param string $event Event name ('before_get', 'after_create', etc.)
	 * @param mixed ...$args Arguments forwarded to callback methods
	 * @return void
	 */
	protected function _trigger(string $event, ...$args): void
	{
		if (!isset($this->{$event}) || !is_array($this->{$event})) {
			return;
		}

		foreach ($this->{$event} as $callback) {
			// Check if global scopes are disabled for before_get callbacks
			if ($event === 'before_get') {
				if ($this->disable_all_scopes || in_array($callback, $this->disabled_scopes, true)) {
					continue;
				}
			}

			if (is_string($callback) && method_exists($this, $callback)) {
				$this->{$callback}(...$args);
			} elseif (is_callable($callback)) {
				call_user_func($callback, ...$args);
			}
		}
	}

	/**
	 * Log database errors and exceptions with context details.
	 *
	 * @param \Throwable $e Exception
	 * @param string $action Database action context
	 * @return void
	 */
	protected function log_database_failure(\Throwable $e, string $action): void
	{
		$last_query = (isset($this->db) && method_exists($this->db, 'last_query'))
			? $this->db->last_query()
			: 'N/A';

		$message = sprintf(
			"[%s] Database failure during '%s' on table '%s'. Last query: %s | Error: %s\nStack trace:\n%s",
			get_class($this),
			$action,
			$this->table ?: 'unknown',
			$last_query,
			$e->getMessage(),
			$e->getTraceAsString()
		);

		log_message('error', $message);
	}
}
