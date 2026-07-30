<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;

/**
 * Role model implementing RoleRepositoryInterface.
 *
 * Handles persistence for the Role entity and syncs
 * associated permissions via role_permissions table.
 */
class Role_model extends CI_Model implements RoleRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Find all roles with their associated permission IDs.
     *
     * @return array Role entities
     */
    public function find_all(): array
    {
        $rows = $this->db
            ->select("
                roles.*,
                GROUP_CONCAT(DISTINCT role_permissions.permission_id ORDER BY role_permissions.permission_id SEPARATOR ',') as permission_ids
            ")
            ->from('roles')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->group_by('roles.id')
            ->order_by('roles.name', 'ASC')
            ->get()
            ->result_array();

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
        $row = $this->db
            ->select("
                roles.*,
                GROUP_CONCAT(DISTINCT role_permissions.permission_id ORDER BY role_permissions.permission_id SEPARATOR ',') as permission_ids
            ")
            ->from('roles')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->where('roles.id', $id)
            ->group_by('roles.id')
            ->get()
            ->row_array();

        return $row ? Role::from_database($row) : null;
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
            $this->db
                ->where('id', $role->get_id())
                ->update('roles', $data);
        } else {
            $this->db->insert('roles', $data);
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
        $this->db
            ->where('id', $id)
            ->delete('roles');
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
     * Count all roles.
     *
     * @return int
     */
    public function count_all(): int
    {
        return (int) $this->db
            ->from('roles')
            ->count_all_results();
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

        $rows = $this->db
            ->from('roles')
            ->select('roles.id, roles.name, roles.slug, roles.description, roles.created_at')
            ->order_by($col, $dir)
            ->limit($length, $start)
            ->get()
            ->result_array();

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

        $this->db
            ->select('COUNT(*) as cnt')
            ->from('roles');

        if ($search !== '') {
            $this->db->group_start()
                ->like('roles.name', $search)
                ->or_like('roles.slug', $search)
                ->group_end();
        }

        return (int) $this->db->get()->row()->cnt;
    }
}
