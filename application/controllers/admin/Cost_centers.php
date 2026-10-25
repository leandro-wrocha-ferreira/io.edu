<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\factories\ModelFactory;
use app\usecases\finance\CreateCostCenterUseCase;
use app\usecases\finance\DeleteCostCenterUseCase;
use app\usecases\finance\GetCostCenterUseCase;
use app\usecases\finance\ListPaginatedCostCentersUseCase;
use app\usecases\finance\UpdateCostCenterUseCase;

/**
 * Cost Centers Controller (Admin)
 *
 * Manages cost center CRUD and DataTables server-side listing in the admin panel.
 */
class Cost_centers extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * List all cost centers view.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'page_name' => 'admin/cost_centers/index',
			'title' => 'Centros de Custo',
			'page_js' => ['admin/cost_centers/index.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTables endpoint for cost centers.
	 *
	 * @return void
	 */
	public function ajax_data()
	{
		$draw = (int) $this->input->get('draw');
		$start = (int) $this->input->get('start');
		$length = (int) $this->input->get('length');
		if ($length <= 0) {
			$length = 25;
		}

		$search = $this->input->get('search');
		$search_value = is_array($search) ? ($search['value'] ?? '') : '';

		$order = $this->input->get('order');
		$order_col_index = is_array($order) && isset($order[0]['column']) ? (int) $order[0]['column'] : 1;
		$order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'asc';

		$col_map = ['id', 'name', 'description', 'status', 'created_at'];
		$order_col = $col_map[$order_col_index] ?? 'name';

		$cost_center_model = ModelFactory::make('cost_center_model');
		$use_case = new ListPaginatedCostCentersUseCase($cost_center_model);
		$result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

		$data = [];
		foreach ($result['data'] as $cost_center) {
			$is_active = $cost_center->is_active();
			$status_badge = $is_active
				? '<span class="edu-badge edu-badge-success edu-badge-dot">Ativo</span>'
				: '<span class="edu-badge edu-badge-neutral edu-badge-dot">Inativo</span>';

			$edit_url = site_url('admin/centros-de-custo/editar/' . $cost_center->get_id());
			$delete_url = site_url('admin/centros-de-custo/excluir/' . $cost_center->get_id());

			$actions = '<div class="edu-action-group">'
				. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Centro de Custo" aria-label="Editar ' . html_escape($cost_center->get_name()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>'
				. '<a href="' . $delete_url . '" class="edu-action-btn edu-action-btn-delete" title="Excluir Centro de Custo" aria-label="Excluir ' . html_escape($cost_center->get_name()) . '" onclick="return confirm(\'Deseja realmente excluir este centro de custo?\');"><i class="bi bi-trash3-fill" aria-hidden="true"></i></a>'
				. '</div>';

			$data[] = [
				'id' => $cost_center->get_id(),
				'name' => html_escape($cost_center->get_name()),
				'description' => html_escape($cost_center->get_description() ?? '—'),
				'status' => $status_badge,
				'created_at' => $cost_center->get_created_at() ? $cost_center->get_created_at()->format('d/m/Y H:i') : '',
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
	 * Show create cost center form and process submission.
	 *
	 * @return void
	 */
	public function create()
	{
		if ($this->input->method() === 'post') {
			$this->form_validation->set_rules('name', 'Nome do Centro de Custo', 'required|trim|min_length[2]|max_length[150]');
			$this->form_validation->set_rules('description', 'Descrição', 'trim|max_length[255]');

			if ($this->form_validation->run() === TRUE) {
				$cost_center_model = ModelFactory::make('cost_center_model');
				$use_case = new CreateCostCenterUseCase($cost_center_model);
				$use_case->execute(
					$this->input->post('name'),
					$this->input->post('description') ?: null,
					$this->input->post('is_active') === '1'
				);

				$this->session->set_flashdata('success', 'Centro de custo cadastrado com sucesso!');
				redirect('admin/centros-de-custo');
			}
		}

		$data = [
			'page_name' => 'admin/cost_centers/form',
			'title' => 'Novo Centro de Custo',
			'cost_center' => null,
			'page_js' => [],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Show update cost center form and process submission.
	 *
	 * @param int $cost_center_id Cost center identifier
	 * @return void
	 */
	public function update(int $cost_center_id)
	{
		if ($this->input->method() === 'post') {
			$this->form_validation->set_rules('name', 'Nome do Centro de Custo', 'required|trim|min_length[2]|max_length[150]');
			$this->form_validation->set_rules('description', 'Descrição', 'trim|max_length[255]');

			if ($this->form_validation->run() === TRUE) {
				$cost_center_model = ModelFactory::make('cost_center_model');
				$update_use_case = new UpdateCostCenterUseCase($cost_center_model);
				$update_use_case->execute(
					(int) $cost_center_id,
					$this->input->post('name'),
					$this->input->post('description') ?: null,
					$this->input->post('is_active') === '1'
				);

				$this->session->set_flashdata('success', 'Centro de custo atualizado com sucesso!');
				redirect('admin/centros-de-custo');
			}
		}

		$cost_center_model = ModelFactory::make('cost_center_model');
		$get_use_case = new GetCostCenterUseCase($cost_center_model);
		$cost_center = $get_use_case->execute((int) $cost_center_id);

		$data = [
			'page_name' => 'admin/cost_centers/form',
			'title' => 'Editar Centro de Custo — ' . $cost_center->get_name(),
			'cost_center' => $cost_center,
			'page_js' => [],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Delete a cost center.
	 *
	 * @param int $cost_center_id Cost center identifier
	 * @return void
	 */
	public function delete(int $cost_center_id)
	{
		$cost_center_model = ModelFactory::make('cost_center_model');
		$use_case = new DeleteCostCenterUseCase($cost_center_model);
		$use_case->execute((int) $cost_center_id);

		$this->session->set_flashdata('success', 'Centro de custo excluído com sucesso!');
		redirect('admin/centros-de-custo');
	}
}
