<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_logs extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id' => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => TRUE,
				'auto_increment' => TRUE
			],
			'user_execute' => [
				'type'       => 'INT',
				'constraint' => 11,
				'unsigned'   => TRUE,
				'null'       => TRUE
			],
			'table' => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
				'null'       => FALSE
			],
			'row' => [
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => TRUE
			],
			'content' => [
				'type' => 'JSON',
				'null' => TRUE
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE
			]
		]);
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('logs');

		$this->db->query("ALTER TABLE `logs` MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
	}

	public function down()
	{
		// No trigger to drop
		$this->dbforge->drop_table('logs');
	}
}
