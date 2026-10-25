<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_product_price_versions extends CI_Migration
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
			'product_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'version_number' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'previous_price_in_cents' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'is_free' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
				'null' => FALSE,
			],
			'new_price_in_cents' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
				'default' => NULL,
			],
			'currency' => [
				'type' => 'VARCHAR',
				'constraint' => 3,
				'default' => 'BRL',
				'null' => FALSE,
			],
			'snapshot' => [
				'type' => 'JSON',
				'null' => FALSE,
			],
			'change_reason' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			],
			'created_by' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('created_at');
		$this->dbforge->add_key('created_by');
		$this->dbforge->create_table('product_price_versions', TRUE);

		$this->db->query("ALTER TABLE `product_price_versions` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			ADD UNIQUE KEY `uq_product_version` (`product_id`, `version_number`)");
	}

	public function down()
	{
		$this->dbforge->drop_table('product_price_versions', TRUE);
	}
}
