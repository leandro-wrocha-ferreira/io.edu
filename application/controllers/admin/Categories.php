<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\factories\ModelFactory;
use app\usecases\category\CreateCategoryUseCase;
use app\usecases\category\DeleteCategoryUseCase;
use app\usecases\category\GetCategoryUseCase;
use app\usecases\category\ListPaginatedCategoriesUseCase;
use app\usecases\category\UpdateCategoryUseCase;

/**
 * Categories Controller (Admin)
 *
 * Manages category CRUD and DataTables server-side listing in the admin panel.
 */
class Categories extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * List all categories view.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'page_name' => 'admin/categories/index',
			'title' => 'Categorias de Cursos',
			'page_js' => ['admin/categories/index.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTables endpoint for categories.
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
		$order_col_index = is_array($order) && isset($order[0]['column']) ? (int) $order[0]['column'] : 0;
		$order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

		$col_map = ['id', 'name', 'slug', 'status', 'created_at'];
		$order_col = $col_map[$order_col_index] ?? 'name';

		$category_model = ModelFactory::make('category_model');
		$use_case = new ListPaginatedCategoriesUseCase($category_model);
		$result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

		$data = [];
		foreach ($result['data'] as $category) {
			$is_active = $category->is_active();
			$status_badge = $is_active
				? '<span class="edu-badge edu-badge-success edu-badge-dot">Ativo</span>'
				: '<span class="edu-badge edu-badge-neutral edu-badge-dot">Inativo</span>';

			$edit_url = site_url('admin/categorias/editar/' . $category->get_id());
			$delete_url = site_url('admin/categorias/excluir/' . $category->get_id());

			$actions = '<div class="edu-action-group">'
				. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Categoria" aria-label="Editar ' . html_escape($category->get_name()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>'
				. '<a href="' . $delete_url . '" class="edu-action-btn edu-action-btn-delete" title="Excluir Categoria" aria-label="Excluir ' . html_escape($category->get_name()) . '" onclick="return confirm(\'Deseja realmente excluir esta categoria?\');"><i class="bi bi-trash3-fill" aria-hidden="true"></i></a>'
				. '</div>';

			$data[] = [
				'id' => $category->get_id(),
				'name' => html_escape($category->get_name()),
				'slug' => '<code>' . html_escape($category->get_slug()) . '</code>',
				'status' => $status_badge,
				'created_at' => $category->get_created_at() ? $category->get_created_at()->format('d/m/Y H:i') : '',
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
	 * Show create category form and process submission.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->form_validation->set_rules('name', 'Nome da Categoria', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[180]');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|in_list[active,inactive]');

		if ($this->form_validation->run() === TRUE) {
			$category_model = ModelFactory::make('category_model');
			$use_case = new CreateCategoryUseCase($category_model);
			$use_case->execute(
				$this->input->post('name'),
				$this->input->post('slug') ?: null,
				$this->input->post('status')
			);

			$this->session->set_flashdata('success', 'Categoria cadastrada com sucesso!');
			redirect('admin/categorias');
			return;
		}

		$data = [
			'page_name' => 'admin/categories/form',
			'title' => 'Nova Categoria',
			'category' => null,
			'page_js' => ['admin/categories/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Show update category form and process submission.
	 *
	 * @param int $category_id Category identifier
	 * @return void
	 */
	public function update(int $category_id)
	{
		$category_model = ModelFactory::make('category_model');
		$get_use_case = new GetCategoryUseCase($category_model);
		$category = $get_use_case->execute((int) $category_id);

		$this->form_validation->set_rules('name', 'Nome da Categoria', 'required|trim|min_length[2]|max_length[150]');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[180]');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|in_list[active,inactive]');

		if ($this->form_validation->run() === TRUE) {
			$update_use_case = new UpdateCategoryUseCase($category_model);
			$update_use_case->execute(
				$category->get_id(),
				$this->input->post('name'),
				$this->input->post('slug') ?: null,
				$this->input->post('status')
			);

			$this->session->set_flashdata('success', 'Categoria atualizada com sucesso!');
			redirect('admin/categorias');
			return;
		}

		$data = [
			'page_name' => 'admin/categories/form',
			'title' => 'Editar Categoria — ' . $category->get_name(),
			'category' => $category,
			'page_js' => ['admin/categories/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Delete a category.
	 *
	 * @param int $category_id Category identifier
	 * @return void
	 */
	public function delete(int $category_id)
	{
		$category_model = ModelFactory::make('category_model');
		$course_model = ModelFactory::make('course_model');
		$use_case = new DeleteCategoryUseCase($category_model, $course_model);
		$use_case->execute((int) $category_id);

		$this->session->set_flashdata('success', 'Categoria excluída com sucesso!');
		redirect('admin/categorias');
	}
}
