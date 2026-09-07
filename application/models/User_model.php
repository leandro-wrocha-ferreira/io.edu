<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

/**
 * User model implementing UserRepositoryInterface.
 *
 * Handles persistence for the User entity using MY_Model explicit CRUD engine.
 * Follows KISS principles: uses clean single-table queries and separate
 * helper queries for RBAC role hydration.
 */
class User_model extends MY_Model implements UserRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'users';

	/**
	 * Target entity class for automatic hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = User::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Explicit filter helper to exclude admin-master users from listing queries.
	 *
	 * Uses a subquery to avoid polluting the main query with outer JOINs.
	 *
	 * @return void
	 */
	public function scope_exclude_admin_master(): void
	{
		$this->db->where("users.id NOT IN (
			SELECT ur.user_id 
			FROM user_roles ur 
			INNER JOIN roles r ON r.id = ur.role_id 
			WHERE r.slug = 'admin-master'
		)", NULL, FALSE);
	}

	/**
	 * Find a user by their ID.
	 *
	 * @param int|string $id User ID
	 * @return User|null User entity or null if not found
	 */
	public function find_by_id($id): ?User
	{
		$row = $this->db
			->where('users.id', (int) $id)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		$row = $this->_hydrate_user_roles($row);
		return $this->to_entity($row);
	}

	/**
	 * Find a user by their email address.
	 *
	 * Queries directly without admin-master exclusion so authentication succeeds for all roles.
	 *
	 * @param Email $email User email (Value Object)
	 * @return User|null User entity or null if not found
	 */
	public function find_by_email(Email $email): ?User
	{
		$row = $this->db
			->where('users.email', (string) $email)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		$row = $this->_hydrate_user_roles($row);
		return $this->to_entity($row);
	}

	/**
	 * Save (insert or update) a user.
	 *
	 * If the user has an ID, performs an update; otherwise inserts a new record.
	 * Also syncs the user_roles association.
	 *
	 * @param User $user User entity to persist
	 * @return void
	 */
	public function save(User $user): void
	{
		$data = [
			'name' => $user->get_name(),
			'email' => (string) $user->get_email(),
			'password' => $user->get_password(),
			'is_active' => $user->is_active() ? 1 : 0,
		];

		if ($user->get_id() !== null) {
			$data['deleted_at'] = $user->is_deleted() ? date('Y-m-d H:i:s') : null;
			$this->update($data, ['id' => $user->get_id()]);
		} else {
			$new_id = $this->insert($data);
			$user->set_id((int) $new_id);
		}

		$this->_sync_user_roles($user);
	}

	/**
	 * Soft delete users matching specified conditions.
	 *
	 * Sets the deleted_at timestamp instead of removing records.
	 *
	 * @param array $where Filter conditions. Usually the primary key (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		return $this->update(['deleted_at' => date('Y-m-d H:i:s')], $where);
	}

	/**
	 * Find all non-deleted users, ordered by creation date DESC.
	 *
	 * @return array User entities
	 */
	public function find_all(): array
	{
		$this->db
			->where('users.deleted_at', NULL)
			->order_by('users.created_at', 'DESC');

		$rows = $this->db->get($this->table)->result_array();
		$rows = $this->_hydrate_batch_user_roles($rows);

		return $this->to_entities($rows);
	}

	/**
	 * Count non-deleted users by role slug.
	 *
	 * @param string $role Role slug (e.g. 'student')
	 * @return int
	 */
	public function count_by_role(string $role): int
	{
		return (int) $this->db
			->join('user_roles', 'user_roles.user_id = users.id')
			->join('roles', 'roles.id = user_roles.role_id')
			->where('roles.slug', $role)
			->where('users.deleted_at', NULL)
			->count_all_results('users');
	}

	/**
	 * Check directly in database if a user has a specific permission.
	 *
	 * @param int $user_id User ID
	 * @param string $permission_slug Permission slug (e.g. 'users.view')
	 * @return bool
	 */
	public function has_permission(int $user_id, string $permission_slug): bool
	{
		$count = (int) $this->db
			->join('user_roles', 'user_roles.user_id = users.id')
			->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
			->join('permissions', 'permissions.id = role_permissions.permission_id')
			->where('users.id', $user_id)
			->where('permissions.slug', $permission_slug)
			->count_all_results('users');

		return $count > 0;
	}

	/**
	 * Hydrate role information for a single user database row array.
	 *
	 * Runs a separate simple query to fetch roles and attach role_ids and primary role slug.
	 *
	 * @param array $row
	 * @return array
	 */
	private function _hydrate_user_roles(array $row): array
	{
		$roles = $this->db
			->select('roles.id, roles.slug')
			->join('roles', 'roles.id = user_roles.role_id')
			->where('user_roles.user_id', (int) $row['id'])
			->get('user_roles')
			->result_array();

		$role_ids = array_map('intval', array_column($roles, 'id'));
		$row['role_ids'] = json_encode($role_ids);
		$row['role'] = !empty($roles) ? $roles[0]['slug'] : null;

		return $row;
	}

	/**
	 * Hydrate role information for multiple user row arrays in batch.
	 *
	 * @param array $rows
	 * @return array
	 */
	private function _hydrate_batch_user_roles(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$user_ids = array_map('intval', array_column($rows, 'id'));
		$user_ids = array_filter(array_unique($user_ids));

		if (empty($user_ids)) {
			return $rows;
		}

		$roles_data = $this->db
			->select('user_roles.user_id, roles.id as role_id, roles.slug')
			->join('roles', 'roles.id = user_roles.role_id')
			->where_in('user_roles.user_id', $user_ids)
			->get('user_roles')
			->result_array();

		$user_roles_map = [];
		foreach ($roles_data as $item) {
			$uid = (int) $item['user_id'];
			if (!isset($user_roles_map[$uid])) {
				$user_roles_map[$uid] = [
					'role_ids' => [],
					'primary_role' => $item['slug'],
				];
			}
			$user_roles_map[$uid]['role_ids'][] = (int) $item['role_id'];
		}

		foreach ($rows as &$row) {
			$uid = (int) $row['id'];
			if (isset($user_roles_map[$uid])) {
				$row['role_ids'] = json_encode($user_roles_map[$uid]['role_ids']);
				$row['role'] = $user_roles_map[$uid]['primary_role'];
			} else {
				$row['role_ids'] = json_encode([]);
				$row['role'] = null;
			}
		}
		unset($row);

		return $rows;
	}

	/**
	 * Sync user_roles for a user.
	 *
	 * Replaces all existing role associations with the current role_ids from the entity.
	 *
	 * @param User $user
	 * @return void
	 */
	private function _sync_user_roles(User $user): void
	{
		$user_id = $user->get_id();
		if ($user_id === null) {
			return;
		}

		$this->db->where('user_id', $user_id)->delete('user_roles');

		$role_ids = $user->get_role_ids();

		if (!empty($role_ids)) {
			$batch = [];
			foreach ($role_ids as $role_id) {
				$batch[] = [
					'user_id' => $user_id,
					'role_id' => (int) $role_id,
				];
			}
			$this->db->insert_batch('user_roles', $batch);
		}
	}

	/**
	 * Count all non-deleted users.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		$this->db->where('users.deleted_at', NULL);
		return parent::count_all();
	}

	/**
	 * Find users for server-side DataTables with search, order, and pagination.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array ['data' => array, 'recordsFiltered' => int]
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$total = $this->_count_paginated($search);

		$allowed = ['id', 'name', 'email', 'created_at'];
		$col = in_array($order_col, $allowed) ? 'users.' . $order_col : 'users.created_at';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db->where('users.deleted_at', NULL);

		if ($search !== '') {
			$this->db->group_start()
				->like('users.name', $search)
				->or_like('users.email', $search)
				->group_end();
		}

		$this->db->order_by($col, $dir)->limit($length, $start);

		$rows = $this->db->get($this->table)->result_array();
		$rows = $this->_hydrate_batch_user_roles($rows);

		return ['data' => $rows, 'recordsFiltered' => $total];
	}

	/**
	 * Count filtered users for DataTables pagination.
	 *
	 * @param string $search Global search term
	 * @return int
	 */
	private function _count_paginated(string $search): int
	{
		$this->db->where('users.deleted_at', NULL);

		if ($search !== '') {
			$this->db->group_start()
				->like('users.name', $search)
				->or_like('users.email', $search)
				->group_end();
		}

		return parent::count_all();
	}
}
