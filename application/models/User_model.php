<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\identity\value_objects\Email;
use app\models\dtos\UserDatabase;
use app\models\mappers\UserMapper;

/**
 * User model implementing UserRepositoryInterface.
 *
 * Handles persistence for the User entity using MY_Model pure data CRUD engine
 * and delegates entity-database mapping exclusively to UserMapper.
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
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a user by their ID.
	 *
	 * @param int $id User ID
	 * @return User|null User entity or null if not found
	 */
	public function find_by_id(int $id): ?User
	{
		$row = $this->db->select('users.*, roles.slug as role')
			->from($this->table)
			->join('user_roles', 'user_roles.user_id = users.id', 'left')
			->join('roles', 'roles.id = user_roles.role_id', 'left')
			->where('users.id', $id)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return UserMapper::to_entity(new UserDatabase($row));
	}

	/**
	 * Find a user by their email address.
	 *
	 * @param Email $email User email (Value Object)
	 * @return User|null User entity or null if not found
	 */
	public function find_by_email(Email $email): ?User
	{
		$row = $this->db->select('users.*, roles.slug as role')
			->from($this->table)
			->join('user_roles', 'user_roles.user_id = users.id', 'left')
			->join('roles', 'roles.id = user_roles.role_id', 'left')
			->where('users.email', (string) $email)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return UserMapper::to_entity(new UserDatabase($row));
	}

	/**
	 * Create a new user record in the database and return the hydrated entity.
	 *
	 * Converts the domain entity to database array via UserMapper, inserts into
	 * the database, and hydrates the newly inserted record back into a User entity.
	 *
	 * @param User $user User entity to persist
	 * @return User Reconstituted User entity from database
	 */
	public function create(User $user): User
	{
		$data = UserMapper::to_database_create($user);
		$insert_id = $this->insert($data);

		$row = $this->db->where($this->primary_key, $insert_id)
			->get($this->table)
			->row_array();

		return UserMapper::to_entity(new UserDatabase($row));
	}

	/**
	 * Save (insert or update) a user.
	 *
	 * If the user has an ID, performs an update; otherwise creates a new record.
	 *
	 * @param User $user User entity to persist
	 * @return User Reconstituted User entity from database
	 */
	public function save(User $user): User
	{
		if ($user->get_id() === null) {
			return $this->create($user);
		}

		$data = UserMapper::to_database_update($user);
		$this->update($data, [$this->primary_key => $user->get_id()]);

		$row = $this->db->where($this->primary_key, $user->get_id())
			->get($this->table)
			->row_array();

		return UserMapper::to_entity(new UserDatabase($row));
	}

	/**
	 * Soft delete a user entity.
	 *
	 * Sets the deleted_at timestamp on the entity and persists it.
	 *
	 * @param User $user User entity to delete
	 * @return User Deleted user entity
	 */
	public function delete(User $user): User
	{
		$user->delete();
		return $this->save($user);
	}

	/**
	 * Find all non-deleted users, ordered by creation date DESC.
	 *
	 * @return array<User> User entities
	 */
	public function find_all(): array
	{
		$this->db->where('users.deleted_at', NULL)
			->order_by('users.created_at', 'DESC');

		$rows = $this->db->get($this->table)->result_array();

		return UserMapper::to_entities($rows);
	}

	/**
	 * Count non-deleted users by role slug.
	 *
	 * @param string $role Role slug (e.g. 'student')
	 * @return int
	 */
	public function count_by_role(string $role): int
	{
		return $this->db->join('user_roles', 'user_roles.user_id = users.id')
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
		$count = $this->db->join('user_roles', 'user_roles.user_id = users.id')
			->join('role_permissions', 'role_permissions.role_id = user_roles.role_id')
			->join('permissions', 'permissions.id = role_permissions.permission_id')
			->where('users.id', $user_id)
			->where('permissions.slug', $permission_slug)
			->count_all_results('users');

		return $count > 0;
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
	 * @return array{data: array<User>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$total = $this->_count_paginated($search);

		$allowed = ['id', 'name', 'email', 'created_at'];
		$col = in_array($order_col, $allowed) ? 'users.' . $order_col : 'users.created_at';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db->select('users.*, roles.slug as role')
			->from($this->table)
			->join('user_roles', 'user_roles.user_id = users.id', 'left')
			->join('roles', 'roles.id = user_roles.role_id', 'left')
			->where('users.deleted_at', NULL);

		if ($search !== '') {
			$this->db->group_start()
				->like('users.name', $search)
				->or_like('users.email', $search)
				->group_end();
		}

		$this->db->order_by($col, $dir)->limit($length, $start);

		$rows = $this->db->get()->result_array();

		return ['data' => UserMapper::to_entities($rows), 'recordsFiltered' => $total];
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

	/**
	 * Sync assigned roles for a user in the user_roles association table.
	 *
	 * @param int $user_id User ID
	 * @param array<int> $role_ids Role IDs to associate
	 * @return void
	 */
	public function sync_user_roles(int $user_id, array $role_ids): void
	{
		parent::destroy_many(['user_id' => $user_id], 'user_roles');

		if (!empty($role_ids)) {
			$batch = [];
			foreach ($role_ids as $role_id) {
				$batch[] = [
					'user_id' => $user_id,
					'role_id' => (int) $role_id,
				];
			}
			parent::insert_many($batch, 'user_roles');
		}
	}

	/**
	 * Find all role slugs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<string> List of role slugs (e.g. ['admin', 'student'])
	 */
	public function find_role_slugs_by_user_id(int $user_id): array
	{
		$rows = $this->db->select('roles.slug')
			->from('user_roles')
			->join('roles', 'roles.id = user_roles.role_id')
			->where('user_roles.user_id', $user_id)
			->get()
			->result_array();

		return array_column($rows, 'slug');
	}

	/**
	 * Find all role IDs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<int> List of role IDs
	 */
	public function find_role_ids_by_user_id(int $user_id): array
	{
		$rows = $this->db->select('role_id')
			->from('user_roles')
			->where('user_id', $user_id)
			->get()
			->result_array();

		return array_map('intval', array_column($rows, 'role_id'));
	}
}
