<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_products extends CI_Migration
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
			'course_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'is_free' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
				'null' => FALSE,
			],
			'price_in_cents' => [
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
			'billing_model' => [
				'type' => 'ENUM("one_time","subscription")',
				'default' => 'one_time',
				'null' => FALSE,
			],
			'max_installments' => [
				'type' => 'TINYINT',
				'constraint' => 3,
				'unsigned' => TRUE,
				'default' => 1,
				'null' => FALSE,
			],
			'subscription_interval' => [
				'type' => 'ENUM("monthly","quarterly","semiannual","yearly")',
				'null' => TRUE,
			],
			'max_billing_cycles' => [
				'type' => 'SMALLINT',
				'constraint' => 5,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'payment_methods' => [
				'type' => 'JSON',
				'null' => FALSE,
			],
			'payment_configuration_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'cost_center_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
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
		$this->dbforge->add_key('payment_configuration_id');
		$this->dbforge->add_key('cost_center_id');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('products', TRUE);

		$this->db->query("ALTER TABLE `products` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			ADD UNIQUE KEY `uq_products_course_id` (`course_id`)");
	}

	public function down()
	{
		$this->dbforge->drop_table('products', TRUE);
	}
}
