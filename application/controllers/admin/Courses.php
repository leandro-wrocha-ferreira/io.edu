<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\factories\ModelFactory;
use app\usecases\category\ListCategoriesUseCase;
use app\usecases\course\ArchiveCourseUseCase;
use app\usecases\course\CreateCourseUseCase;
use app\usecases\course\DeleteCourseUseCase;
use app\usecases\course\GetCourseDetailUseCase;
use app\usecases\course\ListPaginatedCoursesUseCase;
use app\usecases\course\UpdateCourseUseCase;

/**
 * Courses Administration Controller
 *
 * Handles management of courses, detailed view, publication state,
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
				$course_status->is_active() => '<span class="edu-badge edu-badge-success edu-badge-dot">Ativo</span>',
				$course_status->is_inactive() => '<span class="edu-badge edu-badge-neutral edu-badge-dot">Inativo</span>',
				$course_status->is_archived() => '<span class="edu-badge edu-badge-warning edu-badge-dot">Arquivado</span>',
				default => '<span class="edu-badge edu-badge-neutral edu-badge-dot">Rascunho</span>',
			};

			$access_period = $course->get_access_period();
			$access_badge = $access_period->is_lifetime()
				? '<span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-infinity me-1"></i>Vitalício</span>'
				: '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-calendar-event me-1"></i>' . $access_period->get_days() . ' dias</span>';

			$workload = $course->get_workload_in_hours();
			$workload_text = $workload !== null ? $workload . 'h' : '—';

			$edit_url = site_url('admin/cursos/' . $course->get_id() . '/editar');
			$detail_url = site_url('admin/cursos/' . $course->get_id());
			$archive_url = site_url('admin/cursos/arquivar/' . $course->get_id());
			$delete_url = site_url('admin/cursos/excluir/' . $course->get_id());

			$actions = '<div class="edu-action-group">'
				. '<a href="' . $detail_url . '" class="edu-action-btn" title="Ver Detalhes" aria-label="Ver detalhes de ' . html_escape($course->get_title()) . '"><i class="bi bi-eye-fill" aria-hidden="true"></i></a>'
				. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Curso" aria-label="Editar ' . html_escape($course->get_title()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>';

			if (!$course_status->is_archived()) {
				$actions .= '<a href="' . $archive_url . '" class="edu-action-btn" title="Arquivar Curso" aria-label="Arquivar ' . html_escape($course->get_title()) . '" onclick="return confirm(\'Deseja arquivar este curso?\');"><i class="bi bi-archive-fill" aria-hidden="true"></i></a>';
			}

			$actions .= '<a href="' . $delete_url . '" class="edu-action-btn edu-action-btn-delete" title="Excluir Curso" aria-label="Excluir ' . html_escape($course->get_title()) . '" onclick="return confirm(\'Deseja realmente excluir este curso?\');"><i class="bi bi-trash3-fill" aria-hidden="true"></i></a>'
				. '</div>';

			$data[] = [
				'id' => $course->get_id(),
				'title' => '<div class="d-flex align-items-center gap-2">'
					. '<div class="avatar avatar-sm rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" aria-hidden="true"><i class="bi bi-journal-code fs-5"></i></div>'
					. '<div class="min-w-0"><a href="' . $detail_url . '" class="fw-semibold text-body text-decoration-none d-block text-truncate">' . html_escape($course->get_title()) . '</a><small class="text-muted"><code>/' . html_escape((string) $course->get_slug()) . '</code></small></div>'
					. '</div>',
				'category' => html_escape($course->get_category_name() ?? '—'),
				'access' => $access_badge,
				'workload' => $workload_text,
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
	 * Display comprehensive detail view for a specific course.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function detail($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$use_case = new GetCourseDetailUseCase($course_model);
		$course = $use_case->execute($course_id);

		$data = [
			'title' => $course->get_title(),
			'page_name' => 'admin/courses/detail',
			'course' => $course,
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
		$list_categories = new ListCategoriesUseCase($category_model);
		$categories = $list_categories->execute(true);

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
			$use_case = new CreateCourseUseCase($course_model, $category_model);

			$access_days = $this->input->post('access_period_type') === 'limited_time'
				? (int) $this->input->post('access_days')
				: null;

			$workload = $this->input->post('workload_in_hours') !== ''
				? (int) $this->input->post('workload_in_hours')
				: null;

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
				$image_url
			);

			$this->session->set_flashdata('success', 'Curso cadastrado com sucesso!');
			redirect('admin/cursos');
			return;
		}

		$data = [
			'title' => 'Novo Curso',
			'page_name' => 'admin/courses/form',
			'course' => null,
			'categories' => $categories,
			'page_js' => ['admin/courses/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display visual editing form for an existing course and handle submission.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function edit($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$category_model = ModelFactory::make('category_model');

		$detail_use_case = new GetCourseDetailUseCase($course_model);
		$course = $detail_use_case->execute($course_id);

		$list_categories = new ListCategoriesUseCase($category_model);
		$categories = $list_categories->execute(true);

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
			$use_case = new UpdateCourseUseCase($course_model, $category_model);

			$access_days = $this->input->post('access_period_type') === 'limited_time'
				? (int) $this->input->post('access_days')
				: null;

			$workload = $this->input->post('workload_in_hours') !== ''
				? (int) $this->input->post('workload_in_hours')
				: null;

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
				$image_url
			);

			$this->session->set_flashdata('success', 'Curso atualizado com sucesso!');
			redirect('admin/cursos');
			return;
		}

		$data = [
			'title' => 'Editar Curso — ' . $course->get_title(),
			'page_name' => 'admin/courses/form',
			'course' => $course,
			'categories' => $categories,
			'page_js' => ['admin/courses/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Archive a course.
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function archive($course_id = 1)
	{
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
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function delete($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$use_case = new DeleteCourseUseCase($course_model);
		$use_case->execute($course_id);

		$this->session->set_flashdata('success', 'Curso excluído com sucesso!');
		redirect('admin/cursos');
	}

	/**
	 * Display curriculum content hierarchy (Modules -> Sections -> Lessons).
	 *
	 * @param int $course_id Course identifier
	 * @return void
	 */
	public function content($course_id = 1)
	{
		$course_id = (int) $course_id;
		$course_model = ModelFactory::make('course_model');
		$use_case = new GetCourseDetailUseCase($course_model);
		$course = $use_case->execute($course_id);

		$data = [
			'title' => 'Estrutura Curricular — ' . $course->get_title(),
			'page_name' => 'admin/courses/content',
			'course' => [
				'id' => $course->get_id(),
				'title' => $course->get_title(),
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
