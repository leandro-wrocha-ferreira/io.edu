<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\admin\permission\Permission;
use app\domain\admin\permission\PermissionRepositoryInterface;

/**
 * Permission model implementing PermissionRepositoryInterface.
 *
 * Handles persistence for the Permission entity.
 */
class Permission_model extends CI_Model implements PermissionRepositoryInterface
{
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
        $rows = $this->db
            ->from('permissions')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();

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
        $row = $this->db
            ->from('permissions')
            ->where('id', $id)
            ->get()
            ->row_array();

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
            $this->db
                ->where('id', $permission->get_id())
                ->update('permissions', $data);
        } else {
            $this->db->insert('permissions', $data);
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
        $this->db
            ->where('id', $id)
            ->delete('permissions');
    }
}
