<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListPaginatedRolesUseCase;
use app\usecases\admin\GetRoleUseCase;
use app\usecases\admin\CreateRoleUseCase;
use app\usecases\admin\UpdateRoleUseCase;
use app\usecases\admin\DeleteRoleUseCase;
use app\usecases\admin\ListPermissionsUseCase;
use app\factories\ModelFactory;

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

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTable endpoint for roles.
	 *
	 * @return void
	 */
	public function ajax_data()
	{
		$draw = (int) $this->input->get('draw');
		$start = (int) $this->input->get('start');
		$length = (int) $this->input->get('length');
		if ($length <= 0) $length = 25;

		$search = $this->input->get('search');
		$search_value = is_array($search) ? ($search['value'] ?? '') : '';

		$order = $this->input->get('order');
		$order_col_index = is_array($order) && isset($order[0]['column']) ? (int) $order[0]['column'] : 0;
		$order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';

		$col_map = ['id', 'name', 'slug', 'description', 'created_at'];
		$order_col = isset($col_map[$order_col_index]) ? $col_map[$order_col_index] : 'name';

		$use_case = new ListPaginatedRolesUseCase(ModelFactory::make('role_model'));
		$result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

		$data = [];
		foreach ($result['data'] as $row) {
			$edit_url = site_url('admin/perfis/editar/' . $row->get_id());
			$delete_url = site_url('admin/perfis/excluir/' . $row->get_id());

			$data[] = [
				'id' => $row->get_id(),
				'name' => html_escape($row->get_name()),
				'slug' => '<code>' . html_escape($row->get_slug()) . '</code>',
				'description' => $row->get_description() ? html_escape($row->get_description()) : '-',
				'created_at' => $row->get_created_at()->format('d/m/Y H:i'),
				'actions' => '<div class="edu-action-group">'
					. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Perfil" aria-label="Editar ' . html_escape($row->get_name()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>'
					. '<a href="' . $delete_url . '" class="edu-action-btn edu-action-btn-delete" title="Excluir Perfil" aria-label="Excluir ' . html_escape($row->get_name()) . '" onclick="return confirm(\'Tem certeza que deseja excluir este perfil?\')"><i class="bi bi-trash-fill" aria-hidden="true"></i></a>'
					. '</div>',
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
			$use_case = new CreateRoleUseCase(ModelFactory::make('role_model'), ModelFactory::make('permission_model'));
			$permission_ids = $this->input->post('permission_ids') ? (array) $this->input->post('permission_ids') : [];

			$use_case->execute(
				$this->input->post('name'),
				$this->input->post('slug'),
				$this->input->post('description') ?: null,
				array_map('intval', $permission_ids)
			);

			$this->session->set_flashdata('success', 'role_created_success');
			redirect('admin/perfis');
		}

		$permissions_use_case = new ListPermissionsUseCase(ModelFactory::make('permission_model'));
		$data = [
			'page_name' => 'admin/roles/form',
			'title' => 'Novo Perfil',
			'permissions' => $permissions_use_case->execute(),
			'role' => null,
			'role_permission_ids' => [],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Show edit role form and handle submission.
	 *
	 * @param int $id Role ID
	 * @return void
	 */
	public function update(int $id)
	{
		$get_use_case = new GetRoleUseCase(ModelFactory::make('role_model'));
		$role = $get_use_case->execute($id);

		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('slug', 'Slug', 'required|trim|alpha_dash');

		if ($this->form_validation->run() === TRUE) {
			$use_case = new UpdateRoleUseCase(ModelFactory::make('role_model'), ModelFactory::make('permission_model'));
			$permission_ids = $this->input->post('permission_ids') ? (array) $this->input->post('permission_ids') : [];

			$use_case->execute(
				$id,
				$this->input->post('name'),
				$this->input->post('slug'),
				$this->input->post('description') ?: null,
				array_map('intval', $permission_ids)
			);

			$this->session->set_flashdata('success', 'role_updated_success');
			redirect('admin/perfis');
		}

		$permissions_use_case = new ListPermissionsUseCase(ModelFactory::make('permission_model'));
		$data = [
			'page_name' => 'admin/roles/form',
			'title' => 'Editar Perfil',
			'permissions' => $permissions_use_case->execute(),
			'role' => $role,
			'role_permission_ids' => $permissions_use_case->get_ids_by_role_id($id),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Delete a role.
	 *
	 * @param int $id Role ID
	 * @return void
	 */
	public function delete(int $id)
	{
		$use_case = new DeleteRoleUseCase(ModelFactory::make('role_model'));
		$use_case->execute($id);

		$this->session->set_flashdata('success', 'role_deleted_success');
		redirect('admin/perfis');
	}
}
