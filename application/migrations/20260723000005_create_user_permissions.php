<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_user_permissions extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'user_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			),
			'permission_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('user_id', 'permission_id'), FALSE, TRUE);
		$this->dbforge->create_table('user_permissions');
	}

	public function down()
	{
		$this->dbforge->drop_table('user_permissions');
	}
}
