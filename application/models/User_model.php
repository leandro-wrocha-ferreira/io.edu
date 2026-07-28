<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

/**
 * User model implementing UserRepositoryInterface.
 *
 * Handles persistence for the User entity using CI3 Query Builder.
 * Performs soft deletes and joins with user_roles table for RBAC.
 */
class User_model extends CI_Model implements UserRepositoryInterface
{
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
        $row = $this->_build_user_query()
            ->where('users.id', $id)
            ->get()
            ->row_array();

        return $row ? User::from_database($row) : null;
    }

    /**
     * Find a user by their email address.
     *
     * @param Email $email User email (Value Object)
     * @return User|null User entity or null if not found
     */
    public function find_by_email(Email $email): ?User
    {
        $row = $this->_build_user_query()
            ->where('users.email', (string) $email)
            ->get()
            ->row_array();

        return $row ? User::from_database($row) : null;
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
            $this->db
                ->where('id', $user->get_id())
                ->update('users', $data);
        } else {
            $this->db->insert('users', $data);
            $user->set_id((int) $this->db->insert_id());
        }

        $this->_sync_user_roles($user);
    }

    /**
     * Soft delete a user by ID.
     *
     * Sets the deleted_at timestamp instead of removing the record.
     *
     * @param int $id User ID to delete
     * @return void
     */
    public function delete(int $id): void
    {
        $this->db
            ->where('id', $id)
            ->update('users', ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Find all non-deleted users, ordered by creation date DESC.
     *
     * @return array User entities
     */
    public function find_all(): array
    {
        $rows = $this->_build_user_query()
            ->where('users.deleted_at', NULL)
            ->order_by('users.created_at', 'DESC')
            ->get()
            ->result_array();

        return array_map(function (array $row) {
            return User::from_database($row);
        }, $rows);
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
            ->from('users')
            ->join('user_roles', 'user_roles.user_id = users.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('roles.slug', $role)
            ->where('users.deleted_at', NULL)
            ->count_all_results();
    }

    /**
     * Build the base user query with user_roles and roles JOINs.
     *
     * Selects users.*, the primary role slug, and a JSON array of role IDs.
     *
     * @return CI_DB_mysqli_driver
     */
    private function _build_user_query()
    {
        $this->db->flush_cache();

        return $this->db
            ->select("
                users.*,
                ANY_VALUE(roles.slug) as role,
                CONCAT('[', GROUP_CONCAT(DISTINCT user_roles.role_id ORDER BY user_roles.role_id SEPARATOR ','), ']') as role_ids
            ")
            ->from('users')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->group_by('users.id');
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
        $this->db->where('user_id', $user->get_id())->delete('user_roles');

        $role_ids = $user->get_role_ids();

        if (!empty($role_ids)) {
            $batch = [];
            foreach ($role_ids as $role_id) {
                $batch[] = [
                    'user_id' => $user->get_id(),
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
        return (int) $this->db
            ->from('users')
            ->where('deleted_at', NULL)
            ->count_all_results();
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

        $allowed = ['id', 'name', 'email', 'role', 'created_at'];
        $col = in_array($order_col, $allowed) ? ($order_col === 'role' ? 'role' : 'users.' . $order_col) : 'users.created_at';
        $dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

        $query = $this->_build_user_query()
            ->where('users.deleted_at', NULL);

        if ($search !== '') {
            $query->group_start()
                ->like('users.name', $search)
                ->or_like('users.email', $search)
                ->or_like('roles.slug', $search)
                ->group_end();
        }

        $rows = $query->order_by($col, $dir)
            ->limit($length, $start)
            ->get()
            ->result_array();

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
        $this->db->flush_cache();

        $this->db
            ->select('COUNT(DISTINCT users.id) as cnt')
            ->from('users')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->where('users.deleted_at', NULL);

        if ($search !== '') {
            $this->db->group_start()
                ->like('users.name', $search)
                ->or_like('users.email', $search)
                ->or_like('roles.slug', $search)
                ->group_end();
        }

        return (int) $this->db->get()->row()->cnt;
    }
}
