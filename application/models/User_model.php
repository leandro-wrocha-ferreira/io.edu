<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

class User_model extends CI_Model implements UserRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
    }

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

    public function delete(int $id): void
    {
        $this->db
            ->where('id', $id)
            ->update('users', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
