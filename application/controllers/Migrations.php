<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrations extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if ( ! is_cli())
		{
			show_error('This controller can only be accessed from the command line.', 403);
		}
	}

	private function _get_current_version()
	{
		if ( ! $this->db->table_exists('migrations'))
		{
			return '0';
		}

		$row = $this->db->select('version')->get('migrations')->row();
		return $row ? $row->version : '0';
	}

	public function migrate()
	{
		$this->load->library('migration');

		if ($this->migration->latest() === FALSE)
		{
			echo "Migration FAILED:\n";
			echo $this->migration->error_string()."\n";
			exit(1);
		}

		echo "Migration completed successfully.\n";
		echo "Current version: ".$this->_get_current_version()."\n";
	}

	public function rollback($version = NULL)
	{
		if ($version === NULL)
		{
			echo "Usage: php index.php migrations rollback <version>\n";
			exit(1);
		}

		$this->load->library('migration');

		if ($this->migration->version($version) === FALSE)
		{
			echo "Rollback FAILED:\n";
			echo $this->migration->error_string()."\n";
			exit(1);
		}

		echo "Rolled back to version ".$version." successfully.\n";
		echo "Current version: ".$this->_get_current_version()."\n";
	}

	public function status()
	{
		$this->load->library('migration');

		$current = $this->_get_current_version();
		echo "Current migration version: ".$current."\n\n";

		$migrations = $this->migration->find_migrations();

		if (empty($migrations))
		{
			echo "No migration files found.\n";
			return;
		}

		echo "Available migrations:\n";
		echo str_repeat('-', 50)."\n";

		foreach ($migrations as $version => $file)
		{
			$name = basename($file, '.php');
			$status = ($version <= $current) ? '[APPLIED]' : '[PENDING]';
			echo sprintf("%-14s %-30s %s\n", $version, $name, $status);
		}
	}

	public function create($name = NULL)
	{
		if ($name === NULL)
		{
			echo "Usage: php index.php migrations create <migration_name>\n";
			echo "Example: php index.php migrations create create_users_table\n";
			exit(1);
		}

		$timestamp = date('YmdHis');
		$filename = $timestamp.'_'.$name.'.php';
		$path = APPPATH.'migrations/'.$filename;

		if (file_exists($path))
		{
			echo "Migration file already exists: ".$filename."\n";
			exit(1);
		}

		$class_name = 'Migration_'.ucfirst($name);

		$content = "<?php\n";
		$content .= "defined('BASEPATH') OR exit('No direct script access allowed');\n\n";
		$content .= "class ".$class_name." extends CI_Migration {\n\n";
		$content .= "\tpublic function up()\n";
		$content .= "\t{\n";
		$content .= "\t\t// TODO: implement up()\n";
		$content .= "\t}\n\n";
		$content .= "\tpublic function down()\n";
		$content .= "\t{\n";
		$content .= "\t\t// TODO: implement down()\n";
		$content .= "\t}\n";
		$content .= "}\n";

		file_put_contents($path, $content);

		echo "Created migration: ".$filename."\n";
		echo "Path: ".$path."\n";
	}
}
