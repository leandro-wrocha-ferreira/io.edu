<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;

/**
 * Role model implementing RoleRepositoryInterface.
 *
 * Handles persistence for the Role entity using MY_Model explicit CRUD engine.
 * Syncs associated permissions via dedicated KISS helpers without GROUP_CONCAT.
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
	 * Target entity class for automatic hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Role::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Explicit filter to exclude admin-master role from queries.
	 *
	 * @return void
	 */
	public function apply_exclude_admin_master(): void
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
		$this->db->order_by('roles.name', 'ASC');
		$rows = $this->db->get($this->table)->result_array();
		$rows = $this->_hydrate_batch_role_permissions($rows);

		return $this->to_entities($rows);
	}

	/**
	 * Find a role by ID with its associated permission IDs.
	 *
	 * @param int $id Role ID
	 * @return Role|null
	 */
	public function find_by_id($id): ?Role
	{
		$row = $this->db
			->where('roles.id', (int) $id)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		$row = $this->_hydrate_role_permissions($row);
		return $this->to_entity($row);
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
			$this->update($data, ['id' => $role->get_id()]);
		} else {
			$new_id = $this->insert($data);
			$role->set_slug($role->get_slug());
		}

		$this->_sync_role_permissions($role);
	}


	/**
	 * Hydrate permission IDs for a single role row.
	 *
	 * @param array $row
	 * @return array
	 */
	private function _hydrate_role_permissions(array $row): array
	{
		$perms = $this->db
			->select('permission_id')
			->where('role_id', (int) $row['id'])
			->get('role_permissions')
			->result_array();

		$row['permission_ids'] = array_map(static fn($p) => (int) $p['permission_id'], $perms);
		return $row;
	}

	/**
	 * Hydrate permission IDs for multiple role rows in batch.
	 *
	 * @param array $rows
	 * @return array
	 */
	private function _hydrate_batch_role_permissions(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$role_ids = array_map('intval', array_column($rows, 'id'));
		$role_ids = array_filter(array_unique($role_ids));

		if (empty($role_ids)) {
			return $rows;
		}

		$perms_data = $this->db
			->select('role_id, permission_id')
			->where_in('role_id', $role_ids)
			->get('role_permissions')
			->result_array();

		$perm_map = [];
		foreach ($perms_data as $item) {
			$rid = (int) $item['role_id'];
			$perm_map[$rid][] = (int) $item['permission_id'];
		}

		foreach ($rows as &$row) {
			$rid = (int) $row['id'];
			$row['permission_ids'] = $perm_map[$rid] ?? [];
		}
		unset($row);

		return $rows;
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

		if ($search !== '') {
			$this->db->group_start()
				->like('roles.name', $search)
				->or_like('roles.slug', $search)
				->group_end();
		}

		$rows = $this->db->get($this->table)->result_array();

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
		if ($search !== '') {
			$this->db->group_start()
				->like('roles.name', $search)
				->or_like('roles.slug', $search)
				->group_end();
		}

		return (int) $this->db->from($this->table)->count_all_results();
	}
}
