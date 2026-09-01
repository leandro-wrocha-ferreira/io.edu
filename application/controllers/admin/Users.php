<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListUsersUseCase;
use app\usecases\admin\ListPaginatedUsersUseCase;
use app\usecases\admin\GetUserUseCase;
use app\usecases\admin\CreateUserUseCase;
use app\usecases\admin\UpdateUserUseCase;
use app\usecases\admin\ActivateUserUseCase;
use app\usecases\admin\DisableUserUseCase;
use app\usecases\admin\DeleteUserUseCase;
use app\usecases\admin\ListRolesUseCase;
use app\domain\exceptions\ConflictException;

/**
 * Users Controller (Admin)
 *
 * Manages user CRUD and status toggle in the admin panel.
 */
class Users extends MY_Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List all non-deleted users.
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'page_name' => 'admin/users/index',
            'title' => 'Gestão de Usuários',
            'page_js' => ['admin/users/index.js'],
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Server-side DataTable endpoint for users.
     *
     * @return void
     */
    public function ajax_data()
    {
        $draw = (int) $this->input->get('draw', TRUE);
        $start = (int) $this->input->get('start', TRUE);
        $length = (int) $this->input->get('length', TRUE);
        if ($length <= 0) $length = 25;

        $search = $this->input->get('search', TRUE);
        $search_value = is_array($search) ? ($search['value'] ?? '') : '';

        $order = $this->input->get('order', TRUE);
        $order_col_index = is_array($order) && isset($order[0]['column']) ? (int) $order[0]['column'] : 0;
        $order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

        $col_map = ['id', 'name', 'email', 'role', 'created_at'];
        $order_col = isset($col_map[$order_col_index]) ? $col_map[$order_col_index] : 'created_at';

        $current_user_id = (int) $this->session->userdata('user_id');
        $current_user_role = $this->session->userdata('user_role');

        $use_case = new ListPaginatedUsersUseCase();
        $result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

        $data = [];
        foreach ($result['data'] as $row) {
            $role_badge = match ($row['role']) {
                'admin-master' => '<span class="badge-role badge-role-admin"><i class="bi bi-shield-fill-check" aria-hidden="true"></i> Master</span>',
                'admin'        => '<span class="badge-role badge-role-admin"><i class="bi bi-shield-fill" aria-hidden="true"></i> Admin</span>',
                'student'      => '<span class="badge-role badge-role-student"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> Aluno</span>',
                default        => '<span class="badge-role badge-role-default">—</span>',
            };

            $is_active = !empty($row['is_active']);
            $status_badge = $is_active
                ? '<span class="badge-status-active"><i class="bi bi-circle-fill" style="font-size:0.5rem" aria-hidden="true"></i> Ativo</span>'
                : '<span class="badge-status-inactive"><i class="bi bi-circle" style="font-size:0.5rem" aria-hidden="true"></i> Inativo</span>';

            $edit_url = site_url('admin/usuarios/editar/' . $row['id']);
            $toggle_url = $is_active ? site_url('admin/usuarios/desativar/' . $row['id']) : site_url('admin/usuarios/ativar/' . $row['id']);
            $toggle_icon = $is_active ? 'bi-pause-circle' : 'bi-play-circle';
            $toggle_title = $is_active ? 'Desativar' : 'Ativar';

            $is_self = ((int) $row['id'] === $current_user_id);
            $is_admin_target = ($row['role'] === 'admin' || $row['role'] === 'admin-master');

            if ($is_self) {
                $actions = '<span class="text-muted small"><i class="bi bi-person-lock"></i> Próprio usuário</span>';
            } elseif ($is_admin_target && $current_user_role !== 'admin-master') {
                $actions = '<span class="text-muted small"><i class="bi bi-shield-lock"></i> Protegido</span>';
            } else {
                $actions = '<a href="' . $edit_url . '" class="btn-action btn-action-edit" title="Editar"><i class="bi bi-pencil-fill"></i></a> '
                         . '<a href="' . $toggle_url . '" class="btn-action btn-action-toggle" title="' . $toggle_title . '"><i class="bi ' . $toggle_icon . '-fill"></i></a>';
            }

            $data[] = [
                'id' => $row['id'],
                'name' => htmlspecialchars($row['name']),
                'email' => htmlspecialchars($row['email']),
                'role' => $role_badge,
                'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
                'status' => $status_badge,
                'actions' => $actions,
            ];
        }

        json_response([
            'draw' => $draw,
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $data,
        ]);
    }

    /**
     * Show create user form and handle submission.
     *
     * @return void
     */
    public function create()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        $is_admin_master = ($this->session->userdata('user_role') === 'admin-master');

        if ($this->form_validation->run() === TRUE) {
            $use_case = new CreateUserUseCase();
            $role_ids = $this->input->post('role_ids', TRUE) ? (array) $this->input->post('role_ids', TRUE) : [];
            $role_ids = array_map('intval', $role_ids);

            // Non-admin-master users cannot assign AdminMaster (1) or Admin (2) roles
            if (!$is_admin_master) {
                $role_ids = array_filter($role_ids, function ($rid) {
                    return $rid !== 1 && $rid !== 2;
                });
                if (empty($role_ids)) {
                    $role_ids = [3]; // Default to Student
                }
            }

            $use_case->execute(
                $this->input->post('name', TRUE),
                $this->input->post('email', TRUE),
                $this->input->post('password', TRUE),
                $role_ids
            );

            $this->session->set_flashdata('success', 'user_created_success');
            redirect('admin/usuarios');
        }

        $roles_use_case = new ListRolesUseCase();
        $all_roles = $roles_use_case->execute();

        if (!$is_admin_master) {
            $all_roles = array_filter($all_roles, function ($role) {
                return $role->get_slug() !== 'admin-master' && $role->get_slug() !== 'admin';
            });
        }

        $data = [
            'page_name' => 'admin/users/form',
            'title' => 'Novo Usuário',
            'roles' => $all_roles,
            'user' => null,
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Show edit user form and handle submission.
     *
     * @param int $id User ID
     * @return void
     */
    public function update(int $id)
    {
        $current_user_id = (int) $this->session->userdata('user_id');
        $is_admin_master = ($this->session->userdata('user_role') === 'admin-master');

        if ($id === $current_user_id) {
            throw new ConflictException("Você não pode editar ou alterar as configurações do seu próprio usuário nesta tela.");
        }

        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if (!$is_admin_master && ($user->is_admin() || $user->has_role('admin-master'))) {
            throw new ConflictException("Você não tem permissão para alterar usuários administradores.");
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() === TRUE) {
            $use_case = new UpdateUserUseCase();
            $role_ids = $this->input->post('role_ids', TRUE) ? (array) $this->input->post('role_ids', TRUE) : [];
            $role_ids = array_map('intval', $role_ids);

            if (!$is_admin_master) {
                $role_ids = array_filter($role_ids, function ($rid) {
                    return $rid !== 1 && $rid !== 2;
                });
                if (empty($role_ids)) {
                    $role_ids = [3];
                }
            }

            $use_case->execute(
                $id,
                $this->input->post('name', TRUE),
                $this->input->post('email', TRUE),
                $role_ids
            );

            $this->session->set_flashdata('success', 'user_updated_success');
            redirect('admin/usuarios');
        }

        $roles_use_case = new ListRolesUseCase();
        $all_roles = $roles_use_case->execute();

        if (!$is_admin_master) {
            $all_roles = array_filter($all_roles, function ($role) {
                return $role->get_slug() !== 'admin-master' && $role->get_slug() !== 'admin';
            });
        }

        $data = [
            'page_name' => 'admin/users/form',
            'title' => 'Editar Usuário',
            'roles' => $all_roles,
            'user' => $user,
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Activate user.
     *
     * @param int $id User ID
     * @return void
     */
    public function activate(int $id)
    {
        $current_user_id = (int) $this->session->userdata('user_id');
        $is_admin_master = ($this->session->userdata('user_role') === 'admin-master');

        if ($id === $current_user_id) {
            throw new ConflictException("Você não pode alterar o status do seu próprio usuário.");
        }

        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if (!$is_admin_master && ($user->is_admin() || $user->has_role('admin-master'))) {
            throw new ConflictException("Você não tem permissão para alterar usuários administradores.");
        }

        $use_case = new ActivateUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', 'user_activated_success');
        redirect('admin/usuarios');
    }

    /**
     * Disable user.
     *
     * @param int $id User ID
     * @return void
     */
    public function disable(int $id)
    {
        $current_user_id = (int) $this->session->userdata('user_id');
        $is_admin_master = ($this->session->userdata('user_role') === 'admin-master');

        if ($id === $current_user_id) {
            throw new ConflictException("Você não pode alterar o status do seu próprio usuário.");
        }

        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if (!$is_admin_master && ($user->is_admin() || $user->has_role('admin-master'))) {
            throw new ConflictException("Você não tem permissão para alterar usuários administradores.");
        }

        $use_case = new DisableUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', 'user_deactivated_success');
        redirect('admin/usuarios');
    }

    /**
     * Soft-delete a user (removed from all listings).
     *
     * @param int $id User ID
     * @return void
     */
    public function delete(int $id)
    {
        $current_user_id = (int) $this->session->userdata('user_id');
        $is_admin_master = ($this->session->userdata('user_role') === 'admin-master');

        if ($id === $current_user_id) {
            throw new ConflictException("Você não pode excluir o seu próprio usuário.");
        }

        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if (!$is_admin_master && ($user->is_admin() || $user->has_role('admin-master'))) {
            throw new ConflictException("Você não tem permissão para excluir usuários administradores.");
        }

        $use_case = new DeleteUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', 'user_deleted_success');
        redirect('admin/usuarios');
    }
}
