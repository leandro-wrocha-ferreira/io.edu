<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\authorization\Permission;
use app\domain\authorization\repositories\PermissionRepositoryInterface;
use app\models\dtos\PermissionDatabase;
use app\models\mappers\PermissionMapper;

/**
 * Permission model implementing PermissionRepositoryInterface.
 *
 * Handles persistence for the Permission entity using MY_Model pure data CRUD engine
 * and delegates entity-database mapping to PermissionMapper and PermissionDatabase DTO.
 */
class Permission_model extends MY_Model implements PermissionRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'permissions';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find all permissions ordered by name.
	 *
	 * @return array<Permission> List of Permission entities
	 */
	public function find_all(): array
	{
		$this->db->order_by('name', 'ASC');
		$rows = $this->db->get($this->table)->result_array();
		return PermissionMapper::to_entities($rows);
	}

	/**
	 * Find a permission by ID.
	 *
	 * @param int|string $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id(int|string $id): ?Permission
	{
		$row = parent::find_by_id((int) $id);
		if ($row === null) {
			return null;
		}

		return PermissionMapper::to_entity(new PermissionDatabase($row));
	}

	/**
	 * Find permissions by Role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<Permission>
	 */
	public function find_by_role_id(int $role_id): array
	{
		$rows = $this->db->select('permissions.*')
			->from($this->table)
			->join('role_permissions', 'role_permissions.permission_id = permissions.id')
			->where('role_permissions.role_id', $role_id)
			->order_by('permissions.name', 'ASC')
			->get()
			->result_array();

		return PermissionMapper::to_entities($rows);
	}

	/**
	 * Find permission IDs associated with a specific role ID.
	 *
	 * @param int $role_id Role ID
	 * @return array<int> List of permission IDs
	 */
	public function find_ids_by_role_id(int $role_id): array
	{
		$rows = $this->db->select('permission_id')
			->from('role_permissions')
			->where('role_id', $role_id)
			->get()
			->result_array();

		return array_map('intval', array_column($rows, 'permission_id'));
	}

	/**
	 * Sync permissions for a role in the role_permissions association table.
	 *
	 * @param int $role_id Role ID
	 * @param array<int> $permission_ids Permission IDs to associate
	 * @return void
	 */
	public function sync_role_permissions(int $role_id, array $permission_ids): void
	{
		$this->db->where('role_id', $role_id)->delete('role_permissions');

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
	 * Check if a role slug has a specific permission slug.
	 *
	 * @param string $role_slug Role slug (e.g. 'admin')
	 * @param string $permission_slug Permission slug (e.g. 'users.create')
	 * @return bool
	 */
	public function has_role_permission(string $role_slug, string $permission_slug): bool
	{
		$count = (int) $this->db
			->from('role_permissions')
			->join('roles', 'roles.id = role_permissions.role_id')
			->join('permissions', 'permissions.id = role_permissions.permission_id')
			->where('roles.slug', $role_slug)
			->where('permissions.slug', $permission_slug)
			->count_all_results();

		return $count > 0;
	}

	/**
	 * Save (insert or update) a permission entity.
	 *
	 * @param Permission $permission Permission entity to persist
	 * @return Permission Persisted permission entity
	 */
	public function save(Permission $permission): Permission
	{
		if ($permission->get_id() !== null) {
			$data = PermissionMapper::to_database_update($permission);
			$this->update($data, [$this->primary_key => $permission->get_id()]);

			$row = parent::find_by_id($permission->get_id());
			return $row !== null ? PermissionMapper::to_entity(new PermissionDatabase($row)) : $permission;
		}

		$data = PermissionMapper::to_database_create($permission);
		$insert_id = (int) $this->insert($data);

		$row = parent::find_by_id($insert_id);
		return PermissionMapper::to_entity(new PermissionDatabase($row));
	}

	/**
	 * Delete permissions matching specified filter conditions.
	 *
	 * @param array<string, mixed> $where Filter conditions
	 * @return bool
	 */
	public function delete(array $where): bool
	{
		return $this->destroy($where);
	}
}
