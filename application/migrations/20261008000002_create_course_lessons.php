<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_course_lessons extends CI_Migration
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
			'module_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			],
			'title' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'duration_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'sort_order' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'status' => [
				'type' => 'ENUM("draft","published")',
				'default' => 'draft',
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
		$this->dbforge->add_key('module_id');
		$this->dbforge->add_key('slug');
		$this->dbforge->add_key('status');
		$this->dbforge->add_key('sort_order');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('course_lessons', TRUE);

		$this->db->query("ALTER TABLE `course_lessons` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");
	}

	public function down()
	{
		$this->dbforge->drop_table('course_lessons', TRUE);
	}
}
