<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\ListPaginatedUsersUseCase;
use app\usecases\admin\GetUserUseCase;
use app\usecases\admin\CreateUserUseCase;
use app\usecases\admin\UpdateUserUseCase;
use app\usecases\admin\ActivateUserUseCase;
use app\usecases\admin\DisableUserUseCase;
use app\usecases\admin\DeleteUserUseCase;
use app\usecases\admin\ListRolesUseCase;
use app\domain\exceptions\ConflictException;
use app\domain\identity\constants\RoleSlug;
use app\factories\ModelFactory;

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

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTable endpoint for users.
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
		$order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

		$col_map = ['id', 'name', 'email', 'role', 'created_at'];
		$order_col = isset($col_map[$order_col_index]) ? $col_map[$order_col_index] : 'created_at';

		$current_user_id = (int) $this->session->userdata('user_id');
		$is_admin = (bool) $this->session->userdata('is_admin');

		$user_model = ModelFactory::make('user_model');
		$use_case = new ListPaginatedUsersUseCase($user_model);
		$result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

		$data = [];
		foreach ($result['data'] as $user) {
			$primary_role = $user->get_role() ?? '';

			$role_badge = match ($primary_role) {
				RoleSlug::ADMIN => '<span class="badge-role badge-role-admin"><i class="bi bi-shield-fill" aria-hidden="true"></i> Admin</span>',
				RoleSlug::STUDENT => '<span class="badge-role badge-role-student"><i class="bi bi-mortarboard-fill" aria-hidden="true"></i> Aluno</span>',
				default => '<span class="badge-role badge-role-default">—</span>',
			};

			$is_active = $user->is_active();
			$status_badge = $is_active
				? '<span class="badge-status-active"><i class="bi bi-circle-fill" style="font-size:0.5rem" aria-hidden="true"></i> Ativo</span>'
				: '<span class="badge-status-inactive"><i class="bi bi-circle" style="font-size:0.5rem" aria-hidden="true"></i> Inativo</span>';

			$edit_url = site_url('admin/usuarios/editar/' . $user->get_id());
			$toggle_url = $is_active ? site_url('admin/usuarios/desativar/' . $user->get_id()) : site_url('admin/usuarios/ativar/' . $user->get_id());
			$toggle_icon = $is_active ? 'bi-pause-circle' : 'bi-play-circle';
			$toggle_title = $is_active ? 'Desativar' : 'Ativar';

			$is_self = ($user->get_id() === $current_user_id);
			$is_admin_target = ($primary_role === RoleSlug::ADMIN);

			if ($is_self) {
				$actions = '<span class="text-muted small d-inline-flex align-items-center gap-1"><i class="bi bi-person-lock" aria-hidden="true"></i> Próprio usuário</span>';
			} elseif ($is_admin_target && !$is_admin) {
				$actions = '<span class="text-muted small d-inline-flex align-items-center gap-1"><i class="bi bi-shield-lock" aria-hidden="true"></i> Protegido</span>';
			} else {
				$actions = '<div class="edu-action-group">'
					. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Usuário" aria-label="Editar ' . html_escape($user->get_name()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>'
					. '<a href="' . $toggle_url . '" class="edu-action-btn edu-action-btn-toggle" title="' . $toggle_title . ' Usuário" aria-label="' . $toggle_title . ' ' . html_escape($user->get_name()) . '"><i class="bi ' . $toggle_icon . '-fill" aria-hidden="true"></i></a>'
					. '</div>';
			}

			$data[] = [
				'id' => $user->get_id(),
				'name' => html_escape($user->get_name()),
				'email' => html_escape((string) $user->get_email()),
				'role' => $role_badge,
				'created_at' => $user->get_created_at() ? $user->get_created_at()->format('d/m/Y H:i') : '',
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

		$is_admin = (bool) $this->session->userdata('is_admin');

		if ($this->form_validation->run() === TRUE) {
			$use_case = new CreateUserUseCase(
				ModelFactory::make('user_model'),
				ModelFactory::make('role_model')
			);
			$role_ids = $this->input->post('role_ids') ? (array) $this->input->post('role_ids') : [];
			$role_ids = array_map('intval', $role_ids);

			$use_case->execute(
				$this->input->post('name'),
				$this->input->post('email'),
				$this->input->post('password'),
				$role_ids,
				$is_admin
			);

			$this->session->set_flashdata('success', 'user_created_success');
			redirect('admin/usuarios');
		}

		$roles_use_case = new ListRolesUseCase(ModelFactory::make('role_model'));
		$all_roles = $roles_use_case->execute();

		if (!$is_admin) {
			$all_roles = array_filter($all_roles, function ($role) {
				return $role->get_slug() !== RoleSlug::ADMIN;
			});
		}

		$data = [
			'page_name' => 'admin/users/form',
			'title' => 'Novo Usuário',
			'roles' => $all_roles,
			'user' => null,
			'user_role_ids' => [],
		];

		$this->load->view('layout/admin', $data);
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
		$is_admin = (bool) $this->session->userdata('is_admin');

		if ($id === $current_user_id) {
			throw new ConflictException("You cannot edit your own user on this screen");
		}

		$user_model = ModelFactory::make('user_model');
		$get_use_case = new GetUserUseCase($user_model);
		$user = $get_use_case->execute($id);

		$this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

		if ($this->form_validation->run() === TRUE) {
			$use_case = new UpdateUserUseCase($user_model, ModelFactory::make('role_model'));
			$role_ids = $this->input->post('role_ids') ? (array) $this->input->post('role_ids') : [];
			$role_ids = array_map('intval', $role_ids);

			$use_case->execute(
				$id,
				$this->input->post('name'),
				$this->input->post('email'),
				$role_ids,
				$is_admin
			);

			$this->session->set_flashdata('success', 'user_updated_success');
			redirect('admin/usuarios');
		}

		$roles_use_case = new ListRolesUseCase(ModelFactory::make('role_model'));
		$all_roles = $roles_use_case->execute();

		if (!$is_admin) {
			$all_roles = array_filter($all_roles, function ($role) {
				return $role->get_slug() !== RoleSlug::ADMIN;
			});
		}

		$data = [
			'page_name' => 'admin/users/form',
			'title' => 'Editar Usuário',
			'roles' => $all_roles,
			'user' => $user,
			'user_role_ids' => $get_use_case->get_role_ids($id),
		];

		$this->load->view('layout/admin', $data);
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

		if ($id === $current_user_id) {
			throw new ConflictException("You cannot alter the status of your own user");
		}

		$user_model = ModelFactory::make('user_model');
		$use_case = new ActivateUserUseCase($user_model);
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

		if ($id === $current_user_id) {
			throw new ConflictException("You cannot alter the status of your own user");
		}

		$user_model = ModelFactory::make('user_model');
		$use_case = new DisableUserUseCase($user_model);
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

		if ($id === $current_user_id) {
			throw new ConflictException("You cannot delete your own user");
		}

		$user_model = ModelFactory::make('user_model');
		$use_case = new DeleteUserUseCase($user_model);
		$use_case->execute($id);

		$this->session->set_flashdata('success', 'user_deleted_success');
		redirect('admin/usuarios');
	}
}
