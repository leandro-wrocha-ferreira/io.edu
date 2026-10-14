<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_is_required_to_lesson_blocks extends CI_Migration
{
	public function up()
	{
		$fields = [
			'is_required' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'default' => 1,
				'null' => FALSE,
				'after' => 'sort_order',
			],
		];

		$this->dbforge->add_column('lesson_blocks', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('lesson_blocks', 'is_required');
	}
}
