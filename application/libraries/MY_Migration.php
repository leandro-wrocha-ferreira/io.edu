<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration library extension.
 *
 * Records one row per successfully executed migration in the migrations
 * table (version, class, created_at) instead of a single version row.
 */
class MY_Migration extends CI_Migration {

	/**
	 * Constructor.
	 *
	 * Ensures the tracking columns exist when running via CLI, so fresh
	 * installs can record class/created_at from the very first migration.
	 *
	 * @param array $config Migration configuration
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (is_cli())
		{
			$this->_ensure_columns();
		}
	}

	/**
	 * Retrieve the current schema version.
	 *
	 * Reads the highest version recorded, since the table now holds one row
	 * per executed migration.
	 *
	 * @return string Current migration version
	 */
	protected function _get_version()
	{
		$row = $this->db->select_max('version', 'version')
			->get($this->_migration_table)
			->row();

		return $row ? $row->version : '0';
	}

	/**
	 * Record a migration version in the migrations table.
	 *
	 * Truncates log rows above the reached version (rollbacks) and inserts a
	 * new row (version, class, created_at) for migrations that are not logged
	 * yet, preserving the original created_at of already logged versions.
	 * The class/created_at columns are only written when they exist, so
	 * rollbacks that pass through the migration which removes them keep
	 * working instead of failing on a missing column.
	 *
	 * @param string $migration Migration version reached
	 * @return void
	 */
	protected function _update_version($migration)
	{
		$this->db->query(
			'DELETE FROM '.$this->_migration_table
			.' WHERE version > '.$this->db->escape($migration)
		);

		$exists = $this->db->where('version', $migration)
			->get($this->_migration_table)
			->row();

		if ($exists !== NULL)
		{
			return;
		}

		$data = array('version' => $migration);

		if ($this->db->field_exists('class', $this->_migration_table)
			&& $this->db->field_exists('created_at', $this->_migration_table))
		{
			$data['class'] = $this->_migration_class($migration);
			$data['created_at'] = date('Y-m-d H:i:s');
		}

		$this->db->insert($this->_migration_table, $data);
	}

	/**
	 * Derive the migration class name from its filename.
	 *
	 * Mirrors the core naming used in CI_Migration::version().
	 *
	 * @param string $version Migration version
	 * @return string Migration class name
	 */
	private function _migration_class($version)
	{
		$migrations = $this->find_migrations();

		if ( ! isset($migrations[$version]))
		{
			return '';
		}

		$name = basename($migrations[$version], '.php');

		return 'Migration_'.ucfirst(strtolower($this->_get_migration_name($name)));
	}

	/**
	 * Add the tracking columns to the migrations table if missing.
	 *
	 * Keeps the table schema in sync on existing and fresh installs.
	 *
	 * @return void
	 */
	private function _ensure_columns()
	{
		if ( ! $this->db->table_exists($this->_migration_table))
		{
			return;
		}

		if ( ! $this->db->field_exists('class', $this->_migration_table))
		{
			$this->dbforge->add_column($this->_migration_table, array(
				'class' => array(
					'type' => 'VARCHAR',
					'constraint' => 100,
					'null' => TRUE,
					'after' => 'version',
				),
				'created_at' => array(
					'type' => 'DATETIME',
					'null' => TRUE,
					'after' => 'class',
				),
			));
		}
	}
}
