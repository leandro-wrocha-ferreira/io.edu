<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\Price;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\NotFoundException;
use app\factories\ModelFactory;
use app\usecases\category\ListCategoriesUseCase;
use app\usecases\commercial\GetProductByCourseUseCase;
use app\usecases\commercial\ListProductPriceVersionsUseCase;
use app\usecases\commercial\UpdateProductCommercialConditionsUseCase;
use app\usecases\course\ArchiveCourseUseCase;
use app\usecases\course\CreateCourseUseCase;
use app\usecases\course\DeleteCourseUseCase;
use app\usecases\course\GetCourseCurriculumUseCase;
use app\usecases\course\GetCourseDetailUseCase;
use app\usecases\course\ListPaginatedCoursesUseCase;
use app\usecases\course\UpdateCourseUseCase;
use app\usecases\finance\ListActiveCostCentersUseCase;
use app\usecases\finance\ListActivePaymentConfigurationsUseCase;

/**
 * Courses Administration Controller
 *
 * Handles management of courses, commercial configuration, publication state,
 * access periods, and curriculum content hierarchy.
 */
class Courses extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * List all courses with operational search, filters, and metrics.
	 *
	 * @return void
	 */
	public function index()
	{
		$category_model = ModelFactory::make('category_model');
		$list_categories = new ListCategoriesUseCase($category_model);
		$categories = $list_categories->execute(true);

		$data = [
			'title' => 'Gerenciamento de Cursos',
			'page_name' => 'admin/courses/index',
			'categories' => $categories,
			'page_js' => ['admin/courses/index.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTables endpoint for courses.
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

		$category_filter = $this->input->get('category_id');
		$category_id = (!empty($category_filter) && is_numeric($category_filter)) ? (int) $category_filter : null;

		$status_filter = $this->input->get('status');
		$status = (!empty($status_filter)) ? (string) $status_filter : null;

		$col_map = ['id', 'title', 'category_id', 'workload_in_hours', 'status', 'created_at'];
		$order_col = $col_map[$order_col_index] ?? 'title';

		$course_model = ModelFactory::make('course_model');
		$use_case = new ListPaginatedCoursesUseCase($course_model);
		$result = $use_case->execute(
			$start,
			$length,
			$search_value,
			$order_col,
			$order_dir,
			$category_id,
			$status
		);

		$data = [];
		foreach ($result['data'] as $course) {
			$course_status = $course->get_status();
			$status_badge = match (true) {
				$course_status->is_active() => '<span class="badge bg-success-subtle text-success">Ativo</span>',
				$course_status->is_archived() => '<span class="badge bg-warning-subtle text-warning">Arquivado</span>',
				$course_status->is_inactive() => '<span class="badge bg-secondary-subtle text-secondary">Inativo</span>',
				default => '<span class="badge bg-info-subtle text-info">Rascunho</span>'
			};

			$access_text = $course->get_access_period()->is_lifetime()
				? '<span class="text-info"><i class="bi bi-infinity me-1"></i>Vitalício</span>'
				: '<span class="text-muted"><i class="bi bi-calendar-event me-1"></i>' . $course->get_access_period()->get_days() . ' dias</span>';

			$sale_badge = $course->is_available_for_sale()
				? '<span class="badge bg-success-subtle text-success ms-1" title="Disponível para venda"><i class="bi bi-cart-check"></i> Venda Ativa</span>'
				: '<span class="badge bg-secondary-subtle text-muted ms-1" title="Indisponível para venda"><i class="bi bi-cart-x"></i> Venda Inativa</span>';

			$actions = '
				<div class="d-flex align-items-center justify-content-end gap-1">
					<a href="' . base_url('admin/cursos/' . $course->get_id()) . '" class="btn btn-sm btn-icon btn-ghost-secondary" title="Visualizar Detalhes">
						<i class="bi bi-eye"></i>
					</a>
					<a href="' . base_url('admin/cursos/' . $course->get_id() . '/conteudo') . '" class="btn btn-sm btn-icon btn-ghost-primary" title="Gerenciar Currículo & Aulas">
						<i class="bi bi-collection-play"></i>
					</a>
					<a href="' . base_url('admin/cursos/' . $course->get_id() . '/editar') . '" class="btn btn-sm btn-icon btn-ghost-secondary" title="Editar Informações">
						<i class="bi bi-pencil"></i>
					</a>
					<button type="button" class="btn btn-sm btn-icon btn-ghost-danger btn-delete-course" data-id="' . $course->get_id() . '" data-title="' . html_escape($course->get_title()) . '" title="Excluir Curso">
						<i class="bi bi-trash"></i>
					</button>
				</div>
			';

			$data[] = [
				'id' => $course->get_id(),
				'title' => '
					<div class="d-flex align-items-center gap-2">
						' . ($course->get_image() ? '<img src="' . html_escape($course->get_display_image()) . '" alt="" class="rounded-1 border" style="width: 32px; height: 32px; object-fit: cover;">' : '<div class="rounded-1 bg-subtle border d-flex align-items-center justify-content-center text-muted" style="width: 32px; height: 32px;"><i class="bi bi-journal-text"></i></div>') . '
						<div class="d-flex flex-column">
							<a href="' . base_url('admin/cursos/' . $course->get_id()) . '" class="fw-semibold text-heading text-decoration-none">' . html_escape($course->get_title()) . '</a>
							<span class="text-muted small">/' . html_escape((string) $course->get_slug()) . '</span>
						</div>
					</div>
				',
				'category' => html_escape($course->get_category_name() ?? 'Não associada'),
				'workload' => $course->get_workload_in_hours() ? $course->get_workload_in_hours() . 'h' : '—',
				'access' => $access_text,
				'access_period' => $access_text,
				'status' => $status_badge . $sale_badge,
				'created_at' => $course->get_created_at() ? $course->get_created_at()->format('d/m/Y') : '—',
				'actions' => $actions,
			];
		}

		json_response([
			'draw' => $draw,
			'recordsTotal' => $course_model->count_all(),
			'recordsFiltered' => $result['recordsFiltered'],
			'data' => $data,
		]);
	}

	/**
	 * Display comprehensive detail view for a specific course.
	 *
	 * @param int|null $course_id Course identifier
	 * @return void
	 */
	public function detail($course_id = null)
	{
		if ($course_id === null || (int) $course_id <= 0) {
			throw new NotFoundException("Course not found");
		}

		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$product_model = ModelFactory::make('product_model');
		$price_version_model = ModelFactory::make('product_price_version_model');

		$use_case = new GetCourseDetailUseCase($course_model);
		$course = $use_case->execute($course_id);

		$get_product = new GetProductByCourseUseCase($product_model);
		$product = $get_product->execute($course_id);

		$data = [
			'title' => $course->get_title(),
			'page_name' => 'admin/courses/detail',
			'course' => $course,
			'product' => $product,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display course creation form and handle submission.
	 *
	 * @return void
	 */
	public function create()
	{
		$category_model = ModelFactory::make('category_model');
		$payment_config_model = ModelFactory::make('payment_configuration_model');
		$cost_center_model = ModelFactory::make('cost_center_model');

		$list_categories = new ListCategoriesUseCase($category_model);
		$categories = $list_categories->execute(true);

		$list_payment_configs = new ListActivePaymentConfigurationsUseCase($payment_config_model);
		$payment_configurations = $list_payment_configs->execute();

		$list_cost_centers = new ListActiveCostCentersUseCase($cost_center_model);
		$cost_centers = $list_cost_centers->execute();

		$this->form_validation->set_rules('title', 'Título do Curso', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('category_id', 'Categoria', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|in_list[draft,active,inactive,archived]');
		$this->form_validation->set_rules('access_period_type', 'Tipo de Período de Acesso', 'required|trim|in_list[lifetime,limited_time]');

		if ($this->input->post('access_period_type') === 'limited_time') {
			$this->form_validation->set_rules('access_days', 'Dias de Acesso', 'required|is_natural_no_zero', [
				'required' => 'O campo Dias de Acesso é obrigatório quando o acesso for por prazo determinado.',
				'is_natural_no_zero' => 'O campo Dias de Acesso deve ser um número inteiro maior que zero.',
			]);
		}

		$this->form_validation->set_rules('workload_in_hours', 'Carga Horária', 'trim|numeric');
		$this->form_validation->set_rules('short_description', 'Resumo', 'trim|max_length[500]');
		$this->form_validation->set_rules('description', 'Ementa Completa', 'trim');
		$this->form_validation->set_rules('image_url', 'URL da Imagem', 'trim|max_length[255]');
		$this->form_validation->set_rules('target_audience', 'Público-Alvo', 'trim');
		$this->form_validation->set_rules('requirements', 'Pré-requisitos', 'trim');
		$this->form_validation->set_rules('is_free', 'Curso Gratuito', 'trim');
		$this->form_validation->set_rules('is_available_for_sale', 'Disponível para Venda', 'trim');

		$is_free = (bool) $this->input->post('is_free');
		if (!$is_free) {
			$this->form_validation->set_rules('price', 'Preço do Produto', 'required|trim');
			$this->form_validation->set_rules('billing_model', 'Modelo de Cobrança', 'required|trim|in_list[one_time,subscription]');
			if ($this->input->post('billing_model') === 'subscription') {
				$this->form_validation->set_rules('subscription_interval', 'Intervalo de Cobrança', 'required|trim|in_list[monthly,quarterly,semiannual,yearly]');
			} else {
				$this->form_validation->set_rules('max_installments', 'Parcelamento Máximo', 'required|is_natural_no_zero');
			}
		}

		$image_has_error = false;
		if (!empty($_FILES['image_file']['name'])) {
			$file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
			$allowed_exts = ['gif', 'jpg', 'jpeg', 'png', 'webp'];
			$max_size_bytes = 4096 * 1024;

			if (!in_array($file_ext, $allowed_exts, true) || (($_FILES['image_file']['size'] ?? 0) > $max_size_bytes)) {
				$this->session->set_flashdata('error', 'O arquivo enviado deve ser uma imagem válida (GIF, JPG, JPEG, PNG, WEBP) de até 4MB.');
				$image_has_error = true;
			}
		}

		if ($this->form_validation->run() === TRUE && !$image_has_error) {
			$course_model = ModelFactory::make('course_model');
			$product_model = ModelFactory::make('product_model');
			$price_version_model = ModelFactory::make('product_price_version_model');
			$trans_model = ModelFactory::make('transaction_model');

			$use_case = new CreateCourseUseCase(
				$course_model,
				$category_model,
				$product_model,
				$trans_model,
				$price_version_model,
				$payment_config_model,
				$cost_center_model
			);

			$access_days = $this->input->post('access_period_type') === 'limited_time'
				? (int) $this->input->post('access_days')
				: null;

			$workload = $this->input->post('workload_in_hours') !== ''
				? (int) $this->input->post('workload_in_hours')
				: null;

			$video_min_completion_percentage = $this->input->post('video_min_completion_percentage') !== null && $this->input->post('video_min_completion_percentage') !== ''
				? (int) $this->input->post('video_min_completion_percentage')
				: 80;

			$image_type = $this->input->post('image_type');
			$image = null;
			$image_url = ($image_type === 'url') ? ($this->input->post('image_url') ?: null) : null;

			if ($image_type !== 'url' && !empty($_FILES['image_file']['name'])) {
				$uploaded_image = $this->_upload_course_image();
				if ($uploaded_image !== null) {
					$image = $uploaded_image;
					$image_url = null;
				}
			}

			$price_in_cents = null;
			if (!$is_free && $this->input->post('price') !== null && $this->input->post('price') !== '') {
				$price_in_cents = Price::from_string($this->input->post('price'))->get_cents();
			}

			$billing_model = $is_free ? BillingModel::ONE_TIME : ($this->input->post('billing_model') ?: BillingModel::ONE_TIME);
			$max_installments = $is_free ? 1 : (int) ($this->input->post('max_installments') ?: 1);
			$subscription_interval = (!$is_free && $billing_model === BillingModel::SUBSCRIPTION) ? ($this->input->post('subscription_interval') ?: null) : null;
			$max_billing_cycles = (!$is_free && $billing_model === BillingModel::SUBSCRIPTION && $this->input->post('max_billing_cycles') !== '') ? (int) $this->input->post('max_billing_cycles') : null;
			$payment_methods = !$is_free ? (array) ($this->input->post('payment_methods') ?: []) : [];
			$payment_configuration_id = (!$is_free && $this->input->post('payment_configuration_id')) ? (int) $this->input->post('payment_configuration_id') : null;
			$cost_center_id = $this->input->post('cost_center_id') ? (int) $this->input->post('cost_center_id') : null;
			$is_available_for_sale = (bool) $this->input->post('is_available_for_sale');
			$created_by = $this->session->userdata('user_id') ? (int) $this->session->userdata('user_id') : null;

			$use_case->execute(
				(int) $this->input->post('category_id'),
				$this->input->post('title'),
				$this->input->post('slug') ?: null,
				$this->input->post('status'),
				$this->input->post('access_period_type'),
				$access_days,
				$workload,
				$this->input->post('short_description') ?: null,
				$this->input->post('description') ?: null,
				$image,
				0,
				null,
				$this->input->post('target_audience') ?: null,
				$this->input->post('requirements') ?: null,
				$this->input->post('certificate_enabled') ? true : false,
				$image_url,
				$video_min_completion_percentage,
				$is_available_for_sale,
				$is_free,
				$price_in_cents,
				$billing_model,
				$max_installments,
				$subscription_interval,
				$max_billing_cycles,
				$payment_methods,
				$payment_configuration_id,
				$cost_center_id,
				$created_by
			);

			$this->session->set_flashdata('success', 'Curso cadastrado com sucesso!');
			redirect('admin/cursos');
			return;
		}

		$data = [
			'title' => 'Novo Curso',
			'page_name' => 'admin/courses/form',
			'course' => null,
			'product' => null,
			'categories' => $categories,
			'payment_configurations' => $payment_configurations,
			'cost_centers' => $cost_centers,
			'page_js' => ['admin/courses/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display visual editing form for an existing course and handle submission.
	 *
	 * @param int|null $course_id Course identifier
	 * @return void
	 */
	public function edit($course_id = null)
	{
		if ($course_id === null || (int) $course_id <= 0) {
			throw new NotFoundException("Course not found");
		}

		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$category_model = ModelFactory::make('category_model');
		$product_model = ModelFactory::make('product_model');
		$price_version_model = ModelFactory::make('product_price_version_model');
		$payment_config_model = ModelFactory::make('payment_configuration_model');
		$cost_center_model = ModelFactory::make('cost_center_model');
		$trans_model = ModelFactory::make('transaction_model');

		$detail_use_case = new GetCourseDetailUseCase($course_model);
		$course = $detail_use_case->execute($course_id);

		$get_product_use_case = new GetProductByCourseUseCase($product_model);
		$product = $get_product_use_case->execute($course_id);

		$list_categories = new ListCategoriesUseCase($category_model);
		$categories = $list_categories->execute(true);

		$list_payment_configs = new ListActivePaymentConfigurationsUseCase($payment_config_model);
		$payment_configurations = $list_payment_configs->execute();

		$list_cost_centers = new ListActiveCostCentersUseCase($cost_center_model);
		$cost_centers = $list_cost_centers->execute();

		$this->form_validation->set_rules('title', 'Título do Curso', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('category_id', 'Categoria', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('slug', 'Slug', 'trim|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|in_list[draft,active,inactive,archived]');
		$this->form_validation->set_rules('access_period_type', 'Tipo de Período de Acesso', 'required|trim|in_list[lifetime,limited_time]');

		if ($this->input->post('access_period_type') === 'limited_time') {
			$this->form_validation->set_rules('access_days', 'Dias de Acesso', 'required|is_natural_no_zero', [
				'required' => 'O campo Dias de Acesso é obrigatório quando o acesso for por prazo determinado.',
				'is_natural_no_zero' => 'O campo Dias de Acesso deve ser um número inteiro maior que zero.',
			]);
		}

		$this->form_validation->set_rules('workload_in_hours', 'Carga Horária', 'trim|numeric');
		$this->form_validation->set_rules('short_description', 'Resumo', 'trim|max_length[500]');
		$this->form_validation->set_rules('description', 'Ementa Completa', 'trim');
		$this->form_validation->set_rules('image_url', 'URL da Imagem', 'trim|max_length[255]');
		$this->form_validation->set_rules('target_audience', 'Público-Alvo', 'trim');
		$this->form_validation->set_rules('requirements', 'Pré-requisitos', 'trim');
		$this->form_validation->set_rules('is_free', 'Curso Gratuito', 'trim');
		$this->form_validation->set_rules('is_available_for_sale', 'Disponível para Venda', 'trim');

		$is_free = (bool) $this->input->post('is_free');
		if (!$is_free) {
			$this->form_validation->set_rules('price', 'Preço do Produto', 'required|trim');
			$this->form_validation->set_rules('billing_model', 'Modelo de Cobrança', 'required|trim|in_list[one_time,subscription]');
			if ($this->input->post('billing_model') === 'subscription') {
				$this->form_validation->set_rules('subscription_interval', 'Intervalo de Cobrança', 'required|trim|in_list[monthly,quarterly,semiannual,yearly]');
			} else {
				$this->form_validation->set_rules('max_installments', 'Parcelamento Máximo', 'required|is_natural_no_zero');
			}
		}

		$image_has_error = false;
		if (!empty($_FILES['image_file']['name'])) {
			$file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
			$allowed_exts = ['gif', 'jpg', 'jpeg', 'png', 'webp'];
			$max_size_bytes = 4096 * 1024;

			if (!in_array($file_ext, $allowed_exts, true) || (($_FILES['image_file']['size'] ?? 0) > $max_size_bytes)) {
				$this->session->set_flashdata('error', 'O arquivo enviado deve ser uma imagem válida (GIF, JPG, JPEG, PNG, WEBP) de até 4MB.');
				$image_has_error = true;
			}
		}

		if ($this->form_validation->run() === TRUE && !$image_has_error) {
			$use_case = new UpdateCourseUseCase($course_model, $category_model, $product_model);

			$access_days = $this->input->post('access_period_type') === 'limited_time'
				? (int) $this->input->post('access_days')
				: null;

			$workload = $this->input->post('workload_in_hours') !== ''
				? (int) $this->input->post('workload_in_hours')
				: null;

			$video_min_completion_percentage = $this->input->post('video_min_completion_percentage') !== null && $this->input->post('video_min_completion_percentage') !== ''
				? (int) $this->input->post('video_min_completion_percentage')
				: 80;

			$image_type = $this->input->post('image_type');
			$image = $course->get_image();
			$image_url = $course->get_image_url();

			if ($image_type === 'url') {
				$image_url = $this->input->post('image_url') ?: null;
				$image = null;
			} else {
				if (!empty($_FILES['image_file']['name'])) {
					$uploaded_image = $this->_upload_course_image($course->get_id());
					if ($uploaded_image !== null) {
						$image = $uploaded_image;
						$image_url = null;
					}
				}
			}

			$is_available_for_sale = (bool) $this->input->post('is_available_for_sale');

			$price_in_cents = null;
			if (!$is_free && $this->input->post('price') !== null && $this->input->post('price') !== '') {
				$price_in_cents = Price::from_string($this->input->post('price'))->get_cents();
			}

			$billing_model = $is_free ? BillingModel::ONE_TIME : ($this->input->post('billing_model') ?: BillingModel::ONE_TIME);
			$max_installments = $is_free ? 1 : (int) ($this->input->post('max_installments') ?: 1);
			$subscription_interval = (!$is_free && $billing_model === BillingModel::SUBSCRIPTION) ? ($this->input->post('subscription_interval') ?: null) : null;
			$max_billing_cycles = (!$is_free && $billing_model === BillingModel::SUBSCRIPTION && $this->input->post('max_billing_cycles') !== '') ? (int) $this->input->post('max_billing_cycles') : null;
			$payment_methods = !$is_free ? (array) ($this->input->post('payment_methods') ?: []) : [];
			$payment_configuration_id = (!$is_free && $this->input->post('payment_configuration_id')) ? (int) $this->input->post('payment_configuration_id') : null;
			$cost_center_id = $this->input->post('cost_center_id') ? (int) $this->input->post('cost_center_id') : null;
			$change_reason = $this->input->post('change_reason') ?: null;
			$created_by = $this->session->userdata('user_id') ? (int) $this->session->userdata('user_id') : null;

			// 1. Update product commercial conditions first so course can validate eligibility
			if ($product !== null) {
				$update_product = new UpdateProductCommercialConditionsUseCase(
					$product_model,
					$price_version_model,
					$course_model,
					$trans_model,
					$payment_config_model,
					$cost_center_model
				);

				$update_product->execute(
					$product->get_id(),
					$is_free,
					$price_in_cents,
					$billing_model,
					$max_installments,
					$subscription_interval,
					$max_billing_cycles,
					$payment_methods,
					$payment_configuration_id,
					$cost_center_id,
					$change_reason,
					$created_by
				);
			}

			// 2. Update course details & availability
			$use_case->execute(
				$course->get_id(),
				(int) $this->input->post('category_id'),
				$this->input->post('title'),
				$this->input->post('slug') ?: null,
				$this->input->post('status'),
				$this->input->post('access_period_type'),
				$access_days,
				$workload,
				$this->input->post('short_description') ?: null,
				$this->input->post('description') ?: null,
				$image,
				$this->input->post('objectives') ?: null,
				$this->input->post('target_audience') ?: null,
				$this->input->post('requirements') ?: null,
				$this->input->post('certificate_enabled') ? true : false,
				$image_url,
				$video_min_completion_percentage,
				$is_available_for_sale
			);

			$this->session->set_flashdata('success', 'Curso atualizado com sucesso!');
			redirect('admin/cursos');
			return;
		}

		$data = [
			'title' => 'Editar Curso — ' . $course->get_title(),
			'page_name' => 'admin/courses/form',
			'course' => $course,
			'product' => $product,
			'categories' => $categories,
			'payment_configurations' => $payment_configurations,
			'cost_centers' => $cost_centers,
			'page_js' => ['admin/courses/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Archive a course.
	 *
	 * @param int|null $course_id Course identifier
	 * @return void
	 */
	public function archive($course_id = null)
	{
		if ($course_id === null || (int) $course_id <= 0) {
			throw new NotFoundException("Course not found");
		}

		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$use_case = new ArchiveCourseUseCase($course_model);
		$use_case->execute($course_id);

		$this->session->set_flashdata('success', 'Curso arquivado com sucesso!');
		redirect('admin/cursos');
	}

	/**
	 * Delete a course.
	 *
	 * @param int|null $course_id Course identifier
	 * @return void
	 */
	public function delete($course_id = null)
	{
		if ($course_id === null || (int) $course_id <= 0) {
			throw new NotFoundException("Course not found");
		}

		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$use_case = new DeleteCourseUseCase($course_model);
		$use_case->execute($course_id);

		$this->session->set_flashdata('success', 'Curso excluído com sucesso!');
		redirect('admin/cursos');
	}

	/**
	 * Display curriculum content hierarchy (Modules -> Lessons -> Blocks).
	 *
	 * @param int|null $course_id Course identifier
	 * @return void
	 */
	public function content($course_id = null)
	{
		if ($course_id === null || (int) $course_id <= 0) {
			throw new NotFoundException("Course not found");
		}

		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$module_model = ModelFactory::make('course_module_model');
		$lesson_model = ModelFactory::make('course_lesson_model');
		$block_model = ModelFactory::make('lesson_block_model');

		$curriculum_use_case = new GetCourseCurriculumUseCase(
			$course_model,
			$module_model,
			$lesson_model,
			$block_model
		);

		$curriculum_data = $curriculum_use_case->execute($course_id);

		$data = [
			'title' => 'Estrutura Curricular — ' . $curriculum_data['course']->get_title(),
			'page_name' => 'admin/courses/content',
			'course' => $curriculum_data['course'],
			'modules' => $curriculum_data['modules'],
			'metrics' => $curriculum_data['metrics'],
			'page_js' => [
				'admin/courses/curriculum-reorder.js',
				'admin/courses/curriculum.js',
			],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Upload course representation image to dedicated folder.
	 *
	 * @param int|null $course_id Optional course identifier
	 * @return string|null Relative path of uploaded image or null on failure/none
	 */
	private function _upload_course_image(?int $course_id = null): ?string
	{
		if (empty($_FILES['image_file']['name'])) {
			return null;
		}

		$relative_dir = $course_id !== null 
			? 'public/uploads/courses/' . $course_id . '/'
			: 'public/uploads/courses/covers/';

		$upload_dir = FCPATH . $relative_dir;
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0755, true);
		}

		if ($course_id !== null) {
			$existing_files = glob($upload_dir . 'image.*');
			if (!empty($existing_files)) {
				foreach ($existing_files as $file_path) {
					if (is_file($file_path)) {
						@unlink($file_path);
					}
				}
			}
		}

		$config = [
			'upload_path' => $upload_dir,
			'allowed_types' => 'gif|jpg|jpeg|png|webp',
			'max_size' => 4096,
			'file_name' => $course_id !== null ? 'image' : 'cover_' . time() . '_' . bin2hex(random_bytes(4)),
			'overwrite' => $course_id !== null,
		];

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if ($this->upload->do_upload('image_file')) {
			$upload_data = $this->upload->data();
			return $relative_dir . $upload_data['file_name'];
		}

		return null;
	}
}
