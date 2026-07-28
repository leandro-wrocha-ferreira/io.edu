<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration to add created_at/updated_at triggers on users, roles, and permissions tables.
 *
 * These triggers ensure timestamps are managed by the database,
 * not by application code.
 */
class Migration_Add_timestamp_triggers extends CI_Migration {

	public function up()
	{
		// --- users table ---
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

		// --- roles table ---
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

		// --- permissions table ---
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

	public function down()
	{
		$this->db->query("DROP TRIGGER IF EXISTS `trg_users_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_users_update`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_roles_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_roles_update`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_permissions_insert`");
		$this->db->query("DROP TRIGGER IF EXISTS `trg_permissions_update`");
	}
}
