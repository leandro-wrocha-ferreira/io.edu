<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_slug_and_duration_from_course_lessons extends CI_Migration
{
	public function up()
	{
		$table_name = 'course_lessons';

		if ($this->db->field_exists('slug', $table_name)) {
			$this->dbforge->drop_column($table_name, 'slug');
		}

		if ($this->db->field_exists('duration_in_seconds', $table_name)) {
			$this->dbforge->drop_column($table_name, 'duration_in_seconds');
		}
	}

	public function down()
	{
		$fields = [
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			],
			'duration_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
		];
		$this->dbforge->add_column('course_lessons', $fields);
	}
}
