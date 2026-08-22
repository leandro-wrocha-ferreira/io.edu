<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_role_id_from_users extends CI_Migration {

	public function up()
	{
		if ($this->db->field_exists('role_id', 'users'))
		{
			$fk_check = $this->db->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'fk_users_role'");
			if ($fk_check && $fk_check->num_rows() > 0)
			{
				$this->db->query('ALTER TABLE `users` DROP FOREIGN KEY `fk_users_role`');
			}
			$this->db->query('ALTER TABLE `users` DROP COLUMN `role_id`');
		}
	}

	public function down()
	{
		$this->dbforge->add_column('users', array(
			'role_id' => array(
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'null' => TRUE,
			),
		));

		$this->db->update('users', array('role_id' => 2));
		$this->db->query('ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
	}
}
