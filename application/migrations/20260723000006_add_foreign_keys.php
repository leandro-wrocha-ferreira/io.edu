<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_foreign_keys extends CI_Migration {

	public function up()
	{
		$this->db->query('ALTER TABLE `role_permissions` ADD CONSTRAINT `fk_role_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `role_permissions` ADD CONSTRAINT `fk_role_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');

		$this->db->query('ALTER TABLE `user_permissions` ADD CONSTRAINT `fk_user_permissions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `user_permissions` ADD CONSTRAINT `fk_user_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->db->query('ALTER TABLE `role_permissions` DROP FOREIGN KEY `fk_role_permissions_role_id`');
		$this->db->query('ALTER TABLE `role_permissions` DROP FOREIGN KEY `fk_role_permissions_permission_id`');
		$this->db->query('ALTER TABLE `user_permissions` DROP FOREIGN KEY `fk_user_permissions_user_id`');
		$this->db->query('ALTER TABLE `user_permissions` DROP FOREIGN KEY `fk_user_permissions_permission_id`');
	}
}
