<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_lesson_blocks extends CI_Migration
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
			'lesson_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			],
			'type' => [
				'type' => 'ENUM("video","text","attachment","quiz")',
			],
			'payload' => [
				'type' => 'JSON',
			],
			'sort_order' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
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
		$this->dbforge->add_key('lesson_id');
		$this->dbforge->add_key('type');
		$this->dbforge->add_key('sort_order');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('lesson_blocks', TRUE);

		$this->db->query("ALTER TABLE `lesson_blocks` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");
	}

	public function down()
	{
		$this->dbforge->drop_table('lesson_blocks', TRUE);
	}
}
