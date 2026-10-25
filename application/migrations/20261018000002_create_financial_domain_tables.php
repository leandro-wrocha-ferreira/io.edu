<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_financial_domain_tables extends CI_Migration
{
	public function up()
	{
		// 1. payment_providers
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => FALSE,
			],
			'slug' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => FALSE,
				'unique' => TRUE,
			],
			'supported_methods' => [
				'type' => 'JSON',
				'null' => FALSE,
			],
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'null' => FALSE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('payment_providers', TRUE);
		$this->db->query("ALTER TABLE `payment_providers` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

		// 2. payment_credentials
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'provider_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => FALSE,
			],
			'environment' => [
				'type' => 'ENUM("sandbox","production")',
				'default' => 'sandbox',
				'null' => FALSE,
			],
			'api_key_encrypted' => [
				'type' => 'TEXT',
				'null' => FALSE,
			],
			'webhook_secret_encrypted' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'null' => FALSE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('provider_id');
		$this->dbforge->create_table('payment_credentials', TRUE);
		$this->db->query("ALTER TABLE `payment_credentials` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			ADD UNIQUE KEY `uq_cred_provider_id` (`provider_id`, `id`),
			ADD INDEX `idx_credentials_provider_env` (`provider_id`, `environment`, `is_active`)");

		// 3. payment_accounts
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'provider_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'credential_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => FALSE,
			],
			'environment' => [
				'type' => 'ENUM("sandbox","production")',
				'default' => 'sandbox',
				'null' => FALSE,
			],
			'account_identifier' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
				'null' => TRUE,
			],
			'document_number' => [
				'type' => 'VARCHAR',
				'constraint' => 30,
				'null' => TRUE,
			],
			'status' => [
				'type' => 'ENUM("active","pending","suspended")',
				'default' => 'active',
				'null' => FALSE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('provider_id');
		$this->dbforge->add_key('credential_id');
		$this->dbforge->add_key('status');
		$this->dbforge->create_table('payment_accounts', TRUE);
		$this->db->query("ALTER TABLE `payment_accounts` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

		// 4. cost_centers
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
				'null' => FALSE,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'null' => FALSE,
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
		$this->dbforge->add_key('is_active');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('cost_centers', TRUE);
		$this->db->query("ALTER TABLE `cost_centers` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

		// 5. payment_configurations
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
				'null' => FALSE,
			],
			'payment_account_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'allowed_methods' => [
				'type' => 'JSON',
				'null' => FALSE,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'is_active' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
				'null' => FALSE,
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
		$this->dbforge->add_key('payment_account_id');
		$this->dbforge->add_key('is_active');
		$this->dbforge->add_key('deleted_at');
		$this->dbforge->create_table('payment_configurations', TRUE);
		$this->db->query("ALTER TABLE `payment_configurations` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
	}

	public function down()
	{
		$this->dbforge->drop_table('payment_configurations', TRUE);
		$this->dbforge->drop_table('cost_centers', TRUE);
		$this->dbforge->drop_table('payment_accounts', TRUE);
		$this->dbforge->drop_table('payment_credentials', TRUE);
		$this->dbforge->drop_table('payment_providers', TRUE);
	}
}
