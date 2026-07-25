<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

/**
 * User model implementing UserRepositoryInterface.
 *
 * Handles persistence for the User entity using CI3 Query Builder.
 * Performs soft deletes and joins with the roles table for RBAC.
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
        $row = $this->db
            ->select('users.*, roles.slug as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', $id)
            ->where('users.deleted_at', NULL)
            ->get('users')
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
        $row = $this->db
            ->select('users.*, roles.slug as role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', (string) $email)
            ->get('users')
            ->row_array();

        return $row ? User::from_database($row) : null;
    }

    /**
     * Save (insert or update) a user.
     *
     * If the user has an ID, performs an update; otherwise inserts a new record.
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
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($user->get_id() !== null) {
            $this->db
                ->where('id', $user->get_id())
                ->update('users', $data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('users', $data);
        }
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
}
