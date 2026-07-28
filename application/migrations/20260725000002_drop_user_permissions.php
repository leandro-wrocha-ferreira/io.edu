<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Drop_user_permissions extends CI_Migration {

	public function up()
	{
		$this->dbforge->drop_table('user_permissions');
	}

	public function down()
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

		$this->db->query('ALTER TABLE `user_permissions` ADD CONSTRAINT `fk_user_permissions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `user_permissions` ADD CONSTRAINT `fk_user_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}
}
