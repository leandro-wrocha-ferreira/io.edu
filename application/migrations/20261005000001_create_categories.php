<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_categories extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 180,
				'unique' => TRUE,
			],
			'status' => [
				'type' => 'ENUM("active","inactive")',
				'default' => 'active',
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('categories', TRUE);

		$this->db->query("ALTER TABLE `categories` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");
	}

	public function down()
	{
		$this->dbforge->drop_table('categories', TRUE);
	}
}
