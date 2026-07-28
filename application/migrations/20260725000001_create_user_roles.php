<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_user_roles extends CI_Migration {

	public function up()
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
			'role_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('user_id', 'role_id'), FALSE, TRUE);
		$this->dbforge->create_table('user_roles');

		$this->db->query('INSERT INTO user_roles (user_id, role_id) SELECT id, role_id FROM users WHERE role_id IS NOT NULL');

		$this->db->query('ALTER TABLE `user_roles` ADD CONSTRAINT `fk_user_roles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `user_roles` ADD CONSTRAINT `fk_user_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->dbforge->drop_table('user_roles');
	}
}
