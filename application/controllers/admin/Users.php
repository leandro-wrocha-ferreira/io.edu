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

        $use_case = new ListPaginatedUsersUseCase();
        $result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

        $data = [];
        foreach ($result['data'] as $row) {
            $role_badge = match ($row['role']) {
                'admin'   => '<span class="badge-role badge-role-admin"><i class="bi bi-shield-fill" aria-hidden="true"></i> Admin</span>',
                'student' => '<span class="badge-role badge-role-student"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> Aluno</span>',
                default   => '<span class="badge-role badge-role-default">—</span>',
            };

            $is_active = !empty($row['is_active']);
            $status_badge = $is_active
                ? '<span class="badge-status-active"><i class="bi bi-circle-fill" style="font-size:0.5rem" aria-hidden="true"></i> Ativo</span>'
                : '<span class="badge-status-inactive"><i class="bi bi-circle" style="font-size:0.5rem" aria-hidden="true"></i> Inativo</span>';

            $edit_url = site_url('admin/usuarios/editar/' . $row['id']);
            $toggle_url = $is_active ? site_url('admin/usuarios/desativar/' . $row['id']) : site_url('admin/usuarios/ativar/' . $row['id']);
            $toggle_icon = $is_active ? 'bi-pause-circle' : 'bi-play-circle';
            $toggle_title = $is_active ? 'Desativar' : 'Ativar';

            $is_admin = ($row['role'] === 'admin');

            if ($is_admin) {
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

        if ($this->form_validation->run() === TRUE) {
            $use_case = new CreateUserUseCase();
            $role_ids = $this->input->post('role_ids', TRUE) ? (array) $this->input->post('role_ids', TRUE) : [];

            $use_case->execute(
                $this->input->post('name', TRUE),
                $this->input->post('email', TRUE),
                $this->input->post('password', TRUE),
                array_map('intval', $role_ids)
            );

            $this->session->set_flashdata('success', $this->lang->line('user_created_success'));
            redirect('admin/usuarios');
        }

        $roles_use_case = new ListRolesUseCase();
        $data = [
            'page_name' => 'admin/users/form',
            'title' => 'Novo Usuário',
            'roles' => $roles_use_case->execute(),
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
        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if ($user->is_admin()) {
            throw new ConflictException($this->lang->line('user_admin_modify_error'));
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() === TRUE) {
            $use_case = new UpdateUserUseCase();
            $role_ids = $this->input->post('role_ids', TRUE) ? (array) $this->input->post('role_ids', TRUE) : [];

            $use_case->execute(
                $id,
                $this->input->post('name', TRUE),
                $this->input->post('email', TRUE),
                array_map('intval', $role_ids)
            );

            $this->session->set_flashdata('success', $this->lang->line('user_updated_success'));
            redirect('admin/usuarios');
        }

        $roles_use_case = new ListRolesUseCase();
        $data = [
            'page_name' => 'admin/users/form',
            'title' => 'Editar Usuário',
            'roles' => $roles_use_case->execute(),
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
        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if ($user->is_admin()) {
            throw new ConflictException($this->lang->line('user_admin_modify_error'));
        }

        $use_case = new ActivateUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', $this->lang->line('user_activated_success'));
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
        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if ($user->is_admin()) {
            throw new ConflictException($this->lang->line('user_admin_modify_error'));
        }

        $use_case = new DisableUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', $this->lang->line('user_deactivated_success'));
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
        $get_use_case = new GetUserUseCase();
        $user = $get_use_case->execute($id);

        if ($user->is_admin()) {
            throw new ConflictException($this->lang->line('user_admin_delete_error'));
        }

        $use_case = new DeleteUserUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', $this->lang->line('user_deleted_success'));
        redirect('admin/usuarios');
    }
}
