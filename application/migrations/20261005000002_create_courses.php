<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_courses extends CI_Migration
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
			'category_id' => [
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
				'unique' => TRUE,
			],
			'short_description' => [
				'type' => 'VARCHAR',
				'constraint' => 500,
				'null' => TRUE,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'image' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			],
			'status' => [
				'type' => 'ENUM("draft","active","archived")',
				'default' => 'draft',
			],
			'workload_in_hours' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'duration_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'objectives' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'target_audience' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'requirements' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'access_period_type' => [
				'type' => 'ENUM("lifetime","limited_time")',
				'default' => 'limited_time',
			],
			'access_days' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'certificate_enabled' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
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
		$this->dbforge->add_key('category_id');
		$this->dbforge->add_key('status');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('courses', TRUE);

		$this->db->query("ALTER TABLE `courses` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");
	}

	public function down()
	{
		$this->dbforge->drop_table('courses', TRUE);
	}
}
