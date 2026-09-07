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
	 * Target entity class for automatic hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Permission::class;

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
		return parent::find_all();
	}

	/**
	 * Find a permission by ID.
	 *
	 * @param int|string $id Permission ID
	 * @return Permission|null
	 */
	public function find_by_id($id): ?Permission
	{
		return parent::find_by_id($id);
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
			$this->update($data, ['id' => $permission->get_id()]);
		} else {
			$new_id = $this->insert($data);
			$permission->set_name($permission->get_name());
		}
	}

}
