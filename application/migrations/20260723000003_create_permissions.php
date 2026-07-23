<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_permissions extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			),
			'name' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'unique' => TRUE,
			),
			'slug' => array(
				'type' => 'VARCHAR',
				'constraint' => 100,
				'unique' => TRUE,
			),
			'description' => array(
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			),
			'created_at' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'updated_at' => array(
				'type' => 'DATETIME',
				'null' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('permissions');
	}

	public function down()
	{
		$this->dbforge->drop_table('permissions');
	}
}
