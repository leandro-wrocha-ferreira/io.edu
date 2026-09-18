<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_timestamp_triggers extends CI_Migration
{

	public function up()
	{
		// Drop triggers
		$this->db->query("DROP TRIGGER IF EXISTS `trg_users_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_users_update`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_roles_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_roles_update`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_permissions_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_permissions_update`");

		// Alter tables to use MySQL default timestamps
		$this->db->query("ALTER TABLE `users` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");

		$this->db->query("ALTER TABLE `roles` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");

		$this->db->query("ALTER TABLE `permissions` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");
	}

	public function down()
	{
		// Restore DATETIME type
		$this->db->query("ALTER TABLE `users` 
			MODIFY `created_at` DATETIME NULL DEFAULT NULL, 
			MODIFY `updated_at` DATETIME NULL DEFAULT NULL
		");

		$this->db->query("ALTER TABLE `roles` 
			MODIFY `created_at` DATETIME NULL DEFAULT NULL, 
			MODIFY `updated_at` DATETIME NULL DEFAULT NULL
		");

		$this->db->query("ALTER TABLE `permissions` 
			MODIFY `created_at` DATETIME NULL DEFAULT NULL, 
			MODIFY `updated_at` DATETIME NULL DEFAULT NULL
		");

		// Recreate triggers
		$this->db->query("
			CREATE TRIGGER trg_users_insert
			BEFORE INSERT ON `users`
			FOR EACH ROW
			BEGIN
				SET NEW.created_at = COALESCE(NEW.created_at, NOW());
				SET NEW.updated_at = COALESCE(NEW.updated_at, NOW());
			END
		");

		$this->db->query("
			CREATE TRIGGER trg_users_update
			BEFORE UPDATE ON `users`
			FOR EACH ROW
			BEGIN
				SET NEW.updated_at = NOW();
			END
		");

		$this->db->query("
			CREATE TRIGGER trg_roles_insert
			BEFORE INSERT ON `roles`
			FOR EACH ROW
			BEGIN
				SET NEW.created_at = COALESCE(NEW.created_at, NOW());
				SET NEW.updated_at = COALESCE(NEW.updated_at, NOW());
			END
		");

		$this->db->query("
			CREATE TRIGGER trg_roles_update
			BEFORE UPDATE ON `roles`
			FOR EACH ROW
			BEGIN
				SET NEW.updated_at = NOW();
			END
		");

		$this->db->query("
			CREATE TRIGGER trg_permissions_insert
			BEFORE INSERT ON `permissions`
			FOR EACH ROW
			BEGIN
				SET NEW.created_at = COALESCE(NEW.created_at, NOW());
				SET NEW.updated_at = COALESCE(NEW.updated_at, NOW());
			END
		");

		$this->db->query("
			CREATE TRIGGER trg_permissions_update
			BEFORE UPDATE ON `permissions`
			FOR EACH ROW
			BEGIN
				SET NEW.updated_at = NOW();
			END
		");
	}
}
