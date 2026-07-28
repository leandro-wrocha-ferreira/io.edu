<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_is_active_to_users extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_column('users', [
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'after' => 'password',
			],
		]);
	}

	public function down()
	{
		$this->dbforge->drop_column('users', 'is_active');
	}
}
