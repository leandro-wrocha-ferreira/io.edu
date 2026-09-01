<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;

/**
 * Role model implementing RoleRepositoryInterface.
 *
 * Handles persistence for the Role entity using MY_Model lifecycle engine.
 * Syncs associated permissions and applies automatic admin-master scope exclusion.
 */
class Role_model extends MY_Model implements RoleRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'roles';

	/**
	 * Scopes executed automatically before GET/SELECT queries.
	 *
	 * @var array
	 */
	protected array $before_get = ['scope_exclude_admin_master'];

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Centralized global scope to exclude admin-master role from queries.
	 *
	 * Executed automatically by MY_Model lifecycle before query execution.
	 *
	 * @return void
	 */
	protected function scope_exclude_admin_master(): void
	{
		$this->db->where('roles.slug !=', 'admin-master');
	}

	/**
	 * Find all roles with their associated permission IDs.
	 *
	 * @return array Role entities
	 */
	public function find_all(): array
	{
		$this->_build_role_query()
			->order_by('roles.name', 'ASC');

		$rows = $this->get_all();

		return array_map(function (array $row) {
			return Role::from_database($row);
		}, $rows);
	}

	/**
	 * Find a role by ID with its associated permission IDs.
	 *
	 * @param int $id Role ID
	 * @return Role|null
	 */
	public function find_by_id(int $id): ?Role
	{
		$this->_build_role_query();
		$row = $this->get_by_id($id);

		return $row ? Role::from_database($row) : null;
	}

	/**
	 * Build base role query with role_permissions JOIN.
	 *
	 * @return CI_DB_mysqli_driver
	 */
	private function _build_role_query()
	{
		$this->db->flush_cache();

		return $this->db
			->select("
				roles.*,
				GROUP_CONCAT(DISTINCT role_permissions.permission_id ORDER BY role_permissions.permission_id SEPARATOR ',') as permission_ids
			")
			->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
			->group_by('roles.id');
	}

	/**
	 * Save (insert or update) a role.
	 *
	 * Also syncs the associated permissions in role_permissions.
	 *
	 * @param Role $role
	 * @return void
	 */
	public function save(Role $role): void
	{
		$data = [
			'name' => $role->get_name(),
			'slug' => $role->get_slug(),
			'description' => $role->get_description(),
		];

		if ($role->get_id() !== null) {
			$this->update_record($role->get_id(), $data);
		} else {
			$new_id = $this->insert($data);
			$role->set_slug($role->get_slug());
		}

		$this->_sync_role_permissions($role);
	}

	/**
	 * Delete a role by ID.
	 *
	 * @param int $id
	 * @return void
	 */
	public function delete(int $id): void
	{
		$this->delete_record($id);
	}

	/**
	 * Sync role_permissions for a role.
	 *
	 * Replaces all existing permission associations with the current ones.
	 *
	 * @param Role $role
	 * @return void
	 */
	private function _sync_role_permissions(Role $role): void
	{
		$role_id = $role->get_id();

		if ($role_id === null) {
			return;
		}

		$this->db->where('role_id', $role_id)->delete('role_permissions');

		$permission_ids = $role->get_permission_ids();

		if (!empty($permission_ids)) {
			$batch = [];
			foreach ($permission_ids as $perm_id) {
				$batch[] = [
					'role_id' => $role_id,
					'permission_id' => (int) $perm_id,
				];
			}
			$this->db->insert_batch('role_permissions', $batch);
		}
	}

	/**
	 * Find roles for server-side DataTables with search, order, and pagination.
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

		$allowed = ['id', 'name', 'slug', 'description', 'created_at'];
		$col = in_array($order_col, $allowed) ? 'roles.' . $order_col : 'roles.name';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db
			->select('roles.id, roles.name, roles.slug, roles.description, roles.created_at')
			->order_by($col, $dir)
			->limit($length, $start);

		$rows = $this->get_all();

		return ['data' => $rows, 'recordsFiltered' => $total];
	}

	/**
	 * Count filtered roles for DataTables pagination.
	 *
	 * @param string $search Global search term
	 * @return int
	 */
	private function _count_paginated(string $search): int
	{
		$this->db->flush_cache();

		$this->db->select('COUNT(*) as cnt');

		if ($search !== '') {
			$this->db->group_start()
				->like('roles.name', $search)
				->or_like('roles.slug', $search)
				->group_end();
		}

		$row = $this->get_by([]);
		return (int) ($row['cnt'] ?? 0);
	}
}
