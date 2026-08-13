<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Seed default roles and an admin master user.
 *
 * Destructive data migration: clears roles, permissions (id >= 11),
 * user roles and users, then recreates the default roles
 * (AdminMaster, Admin, Student) and a single admin master user.
 */
class Migration_Seed_roles_and_admin_master extends CI_Migration {

	public function up()
	{
		$this->db->query('DELETE FROM role_permissions');
		$this->db->query('ALTER TABLE role_permissions AUTO_INCREMENT = 1');

		$this->db->query('DELETE FROM permissions WHERE id >= 11');
		$this->db->query('ALTER TABLE permissions AUTO_INCREMENT = 11');

		$this->db->query('DELETE FROM user_roles');
		$this->db->query('ALTER TABLE user_roles AUTO_INCREMENT = 1');

		$this->db->query('DELETE FROM users');
		$this->db->query('ALTER TABLE users AUTO_INCREMENT = 1');

		$this->db->query('DELETE FROM roles');
		$this->db->query('ALTER TABLE roles AUTO_INCREMENT = 1');

		$this->db->insert('roles', array(
			'id' => 1,
			'name' => 'AdminMaster',
			'slug' => 'admin-master',
			'description' => 'Full access to all system resources',
		));

		$this->db->insert('roles', array(
			'id' => 2,
			'name' => 'Admin',
			'slug' => 'admin',
			'description' => 'Administrative access',
		));

		$this->db->insert('roles', array(
			'id' => 3,
			'name' => 'Student',
			'slug' => 'student',
			'description' => 'Student access',
		));

		$this->db->insert('users', array(
			'name' => 'admin master',
			'email' => 'adminmaster@inverta.com',
			'password' => password_hash('admin', PASSWORD_BCRYPT),
			'is_active' => 1,
		));

		$this->db->insert('user_roles', array(
			'user_id' => $this->db->insert_id(),
			'role_id' => 1,
		));
	}

	public function down()
	{
		$this->db->query('DELETE FROM user_roles WHERE role_id IN (1, 2, 3)');
		$this->db->query('DELETE FROM users WHERE email = '.$this->db->escape('adminmaster@inverta.com'));
		$this->db->query('DELETE FROM roles WHERE id IN (1, 2, 3)');
	}
}
