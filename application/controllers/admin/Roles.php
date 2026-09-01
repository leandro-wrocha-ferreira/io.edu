<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListRolesUseCase;
use app\usecases\admin\ListPaginatedRolesUseCase;
use app\usecases\admin\GetRoleUseCase;
use app\usecases\admin\CreateRoleUseCase;
use app\usecases\admin\UpdateRoleUseCase;
use app\usecases\admin\DeleteRoleUseCase;
use app\usecases\admin\ListPermissionsUseCase;

/**
 * Roles Controller (Admin)
 *
 * Manages role CRUD in the admin panel.
 */
class Roles extends MY_Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List all roles.
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'page_name' => 'admin/roles/index',
            'title' => 'Perfis e Permissões',
            'page_js' => ['admin/roles/index.js'],
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Server-side DataTable endpoint for roles.
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
        $order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';

        $col_map = ['id', 'name', 'slug', 'description', 'created_at'];
        $order_col = isset($col_map[$order_col_index]) ? $col_map[$order_col_index] : 'name';

        $use_case = new ListPaginatedRolesUseCase();
        $result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

        $data = [];
        foreach ($result['data'] as $row) {
            $edit_url = site_url('admin/perfis/editar/' . $row['id']);
            $delete_url = site_url('admin/perfis/excluir/' . $row['id']);

            $data[] = [
                'id' => $row['id'],
                'name' => htmlspecialchars($row['name']),
                'slug' => '<code>' . htmlspecialchars($row['slug']) . '</code>',
                'description' => $row['description'] ? htmlspecialchars($row['description']) : '-',
                'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
                'actions' => '<a href="' . $edit_url . '" class="btn-action btn-action-edit" title="Editar"><i class="bi bi-pencil-fill"></i></a> '
                    . '<a href="' . $delete_url . '" class="btn-action btn-action-delete" title="Excluir" onclick="return confirm(\'Tem certeza?\')"><i class="bi bi-trash-fill"></i></a>',
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
     * Show create role form and handle submission.
     *
     * @return void
     */
    public function create()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('slug', 'Slug', 'required|trim|alpha_dash');

        if ($this->form_validation->run() === TRUE) {
            $use_case = new CreateRoleUseCase();
            $permission_ids = $this->input->post('permission_ids', TRUE) ? (array) $this->input->post('permission_ids', TRUE) : [];

            $use_case->execute(
                $this->input->post('name', TRUE),
                $this->input->post('slug', TRUE),
                $this->input->post('description', TRUE) ?: null,
                array_map('intval', $permission_ids)
            );

            $this->session->set_flashdata('success', 'role_created_success');
            redirect('admin/perfis');
        }

        $permissions_use_case = new ListPermissionsUseCase();
        $data = [
            'page_name' => 'admin/roles/form',
            'title' => 'Novo Perfil',
            'permissions' => $permissions_use_case->execute(),
            'role' => null,
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Show edit role form and handle submission.
     *
     * @param int $id Role ID
     * @return void
     */
    public function update(int $id)
    {
        $get_use_case = new GetRoleUseCase();
        $role = $get_use_case->execute($id);

        if ($id === 1 || $role->get_slug() === 'admin-master') {
            throw new \app\domain\exceptions\ConflictException("O perfil AdminMaster é protegido e não pode ser alterado.");
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('slug', 'Slug', 'required|trim|alpha_dash');

        if ($this->form_validation->run() === TRUE) {
            $use_case = new UpdateRoleUseCase();
            $permission_ids = $this->input->post('permission_ids', TRUE) ? (array) $this->input->post('permission_ids', TRUE) : [];

            $use_case->execute(
                $id,
                $this->input->post('name', TRUE),
                $this->input->post('slug', TRUE),
                $this->input->post('description', TRUE) ?: null,
                array_map('intval', $permission_ids)
            );

            $this->session->set_flashdata('success', 'role_updated_success');
            redirect('admin/perfis');
        }

        $permissions_use_case = new ListPermissionsUseCase();
        $data = [
            'page_name' => 'admin/roles/form',
            'title' => 'Editar Perfil',
            'permissions' => $permissions_use_case->execute(),
            'role' => $role,
        ];

        $this->load->view('admin/index', $data);
    }

    /**
     * Delete a role.
     *
     * @param int $id Role ID
     * @return void
     */
    public function delete(int $id)
    {
        $get_use_case = new GetRoleUseCase();
        $role = $get_use_case->execute($id);

        if ($id === 1 || $role->get_slug() === 'admin-master') {
            throw new \app\domain\exceptions\ConflictException("O perfil AdminMaster é protegido e não pode ser excluído.");
        }

        $use_case = new DeleteRoleUseCase();
        $use_case->execute($id);

        $this->session->set_flashdata('success', 'role_deleted_success');
        redirect('admin/perfis');
    }
}
