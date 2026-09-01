<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\admin\permission\Permission;
use app\domain\admin\permission\PermissionRepositoryInterface;

/**
 * Permission model implementing PermissionRepositoryInterface.
 *
 * Handles persistence for the Permission entity using MY_Model base CRUD methods.
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
	 * @return array Permission entities
	 */
	public function find_all(): array
	{
		$this->db->order_by('name', 'ASC');
		$rows = $this->get_all();

		return array_map(function (array $row) {
			return Permission::from_database($row);
		}, $rows);
	}

	/**
	 * Find a permission by ID.
	 *
	 * @param int $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id(int $id): ?Permission
	{
		$row = $this->get_by_id($id);
		return $row ? Permission::from_database($row) : null;
	}

	/**
	 * Save (insert or update) a permission.
	 *
	 * @param Permission $permission
	 * @return void
	 */
	public function save(Permission $permission): void
	{
		$data = [
			'name' => $permission->get_name(),
			'slug' => $permission->get_slug(),
			'description' => $permission->get_description(),
		];

		if ($permission->get_id() !== null) {
			$this->update_record($permission->get_id(), $data);
		} else {
			$new_id = $this->insert($data);
			$permission->set_name($permission->get_name());
		}
	}

	/**
	 * Delete a permission by ID.
	 *
	 * @param int $id
	 * @return void
	 */
	public function delete(int $id): void
	{
		$this->delete_record($id);
	}
}
