<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_video_min_completion_percentage_to_courses extends CI_Migration
{
	public function up()
	{
		$fields = [
			'video_min_completion_percentage' => [
				'type' => 'INT',
				'constraint' => 3,
				'unsigned' => TRUE,
				'default' => 80,
				'null' => FALSE,
				'after' => 'duration_in_seconds',
			],
		];

		$this->dbforge->add_column('courses', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('courses', 'video_min_completion_percentage');
	}
}
