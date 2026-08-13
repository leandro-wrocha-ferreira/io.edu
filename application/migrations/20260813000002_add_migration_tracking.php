<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Add version/class/created_at tracking to the migrations table.
 *
 * Adds the class and created_at columns and backfills one row for every
 * migration already applied, so the table holds a complete execution log.
 */
class Migration_Add_migration_tracking extends CI_Migration {

	public function up()
	{
		$this->_ensure_columns();

		$this->db->query('DELETE FROM migrations WHERE version = 0');

		$applied = $this->_applied_migrations();

		$rows = $this->db->select('version')
			->get('migrations')
			->result_array();

		$existing = array();
		foreach ($rows as $row)
		{
			$existing[(string) $row['version']] = TRUE;
		}

		foreach ($applied as $version => $class)
		{
			$key = (string) $version;
			$data = array(
				'class' => $class,
				'created_at' => $this->_created_at_from_version($version),
			);

			if (isset($existing[$key]))
			{
				$this->db->where('version', $version)
					->where('(class IS NULL OR created_at IS NULL)', NULL, FALSE)
					->update('migrations', $data);
			}
			else
			{
				$data['version'] = $version;
				$this->db->insert('migrations', $data);
			}
		}
	}

	public function down()
	{
		$this->db->query(
			'DELETE FROM migrations WHERE version <> '
			.'(SELECT v FROM (SELECT MAX(version) AS v FROM migrations) AS t)'
		);

		$this->dbforge->drop_column('migrations', 'created_at');
		$this->dbforge->drop_column('migrations', 'class');
	}

	/**
	 * Add the tracking columns to the migrations table if missing.
	 *
	 * @return void
	 */
	private function _ensure_columns()
	{
		if ( ! $this->db->table_exists('migrations'))
		{
			return;
		}

		if ( ! $this->db->field_exists('class', 'migrations'))
		{
			$this->dbforge->add_column('migrations', array(
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

	/**
	 * List every migration file older than this one, keyed by version,
	 * with the class name derived from the filename.
	 *
	 * @return array Version => class name
	 */
	private function _applied_migrations()
	{
		$current = (int) substr(basename(__FILE__), 0, 14);
		$applied = array();

		foreach (glob(APPPATH.'migrations'.DIRECTORY_SEPARATOR.'*_*.php') as $file)
		{
			$name = basename($file, '.php');

			if ( ! preg_match('/^\d{14}_(\w+)$/', $name, $matches))
			{
				continue;
			}

			$version = (int) substr($name, 0, 14);

			if ($version >= $current)
			{
				continue;
			}

			$applied[$version] = 'Migration_'.ucfirst(strtolower($matches[1]));
		}

		ksort($applied);
		return $applied;
	}

	/**
	 * Convert a migration version timestamp into a datetime value.
	 *
	 * @param int $version Migration version
	 * @return string Datetime string
	 */
	private function _created_at_from_version($version)
	{
		$v = (string) $version;

		return substr($v, 0, 4).'-'.substr($v, 4, 2).'-'.substr($v, 6, 2)
			.' '.substr($v, 8, 2).':'.substr($v, 10, 2).':'.substr($v, 12, 2);
	}
}
