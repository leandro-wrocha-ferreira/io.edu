<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_image_url_to_courses extends CI_Migration
{
	public function up()
	{
		$fields = [
			'image_url' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
				'after' => 'image',
			],
		];
		$this->dbforge->add_column('courses', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('courses', 'image_url');
	}
}
