<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\Role;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\models\dtos\RoleDatabase;
use app\models\mappers\RoleMapper;

/**
 * Role model implementing RoleRepositoryInterface.
 *
 * Handles persistence for the Role entity using MY_Model pure data CRUD engine
 * and delegates entity-database mapping to RoleMapper and RoleDatabase DTO.
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
	 * Find all roles.
	 *
	 * @return array<Role> List of hydrated Role entities
	 */
	public function find_all(): array
	{
		$this->db->order_by('roles.name', 'ASC');
		$rows = $this->db->get($this->table)->result_array();
		return RoleMapper::to_entities($rows);
	}

	/**
	 * Find a role by ID.
	 *
	 * @param int|string $id Role ID
	 * @return Role|null
	 */
	public function find_by_id(int|string $id): ?Role
	{
		$row = parent::find_by_id((int) $id);
		if ($row === null) {
			return null;
		}

		return RoleMapper::to_entity(new RoleDatabase($row));
	}

	/**
	 * Find a role by unique slug.
	 *
	 * @param string $slug Role slug
	 * @return Role|null
	 */
	public function find_by_slug(string $slug): ?Role
	{
		$row = $this->db->where('slug', $slug)->get($this->table)->row_array();
		if ($row === null) {
			return null;
		}

		return RoleMapper::to_entity(new RoleDatabase($row));
	}

	/**
	 * Save (insert or update) a role entity.
	 *
	 * @param Role $role Role entity to persist
	 * @return Role Persisted role entity
	 */
	public function save(Role $role): Role
	{
		if ($role->get_id() !== null) {
			$data = RoleMapper::to_database_update($role);
			$this->update($data, [$this->primary_key => $role->get_id()]);

			$row = parent::find_by_id($role->get_id());
			return $row !== null ? RoleMapper::to_entity(new RoleDatabase($row)) : $role;
		}

		$data = RoleMapper::to_database_create($role);
		$insert_id = (int) $this->insert($data);

		$row = parent::find_by_id($insert_id);
		return RoleMapper::to_entity(new RoleDatabase($row));
	}

	/**
	 * Delete roles matching specified conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		return $this->destroy($where);
	}

	/**
	 * Find roles for server-side DataTables with search, order, and pagination.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<Role>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$total = $this->_count_paginated($search);

		$allowed = ['id', 'name', 'slug', 'description', 'created_at'];
		$col = in_array($order_col, $allowed) ? 'roles.' . $order_col : 'roles.name';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db->select('roles.id, roles.name, roles.slug, roles.description, roles.created_at')
			->order_by($col, $dir)
			->limit($length, $start);

		if ($search !== '') {
			$this->db->group_start()
				->like('roles.name', $search)
				->or_like('roles.slug', $search)
				->group_end();
		}

		$rows = $this->db->get($this->table)->result_array();

		return ['data' => RoleMapper::to_entities($rows), 'recordsFiltered' => $total];
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

		return $this->db->from($this->table)->count_all_results();
	}
}
