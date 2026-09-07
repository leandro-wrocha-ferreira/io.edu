<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\NotFoundException;
use app\usecases\education\CreateCourseUseCase;
use app\usecases\education\ListCoursesUseCase;
use app\usecases\education\UpdateCourseUseCase;

/**
 * Courses Controller (Admin)
 */
class Courses extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('course_model');
	}

	/**
	 * List courses view.
	 *
	 * @return void
	 */
	public function index(): void
	{
		$use_case = new ListCoursesUseCase();
		$courses = $use_case->execute();

		$data = [
			'page_name' => 'admin/courses/index',
			'title'     => 'Gestão de Cursos',
			'courses'   => $courses,
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Create course form and action.
	 *
	 * @return void
	 */
	public function create(): void
	{
		$this->form_validation->set_rules('title', 'Título', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('description', 'Descrição', 'trim');
		$this->form_validation->set_rules('base_price', 'Preço Base', 'trim|numeric');

		if ($this->form_validation->run() === TRUE) {
			$title = $this->input->post('title', TRUE);
			$description = $this->input->post('description', TRUE) ?: null;
			$base_price = (float) ($this->input->post('base_price', TRUE) ?: 0.0);
			$category_id = $this->input->post('category_id', TRUE) ? (int) $this->input->post('category_id', TRUE) : null;

			$use_case = new CreateCourseUseCase();
			$use_case->execute($title, $base_price, $description, $category_id);

			$this->session->set_flashdata('success', 'Curso criado com sucesso.');
			redirect('admin/cursos');
		}

		$data = [
			'page_name' => 'admin/courses/form',
			'title'     => 'Novo Curso',
			'course'    => null,
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Edit course form and action.
	 *
	 * @param int $id Course ID
	 * @return void
	 */
	public function edit(int $id): void
	{
		$course = $this->course_model->find_by_id($id);
		if ($course === null) {
			throw new NotFoundException("Curso não encontrado.");
		}

		$this->form_validation->set_rules('title', 'Título', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('description', 'Descrição', 'trim');
		$this->form_validation->set_rules('base_price', 'Preço Base', 'trim|numeric');

		if ($this->form_validation->run() === TRUE) {
			$title = $this->input->post('title', TRUE);
			$description = $this->input->post('description', TRUE) ?: null;
			$base_price = (float) ($this->input->post('base_price', TRUE) ?: 0.0);
			$category_id = $this->input->post('category_id', TRUE) ? (int) $this->input->post('category_id', TRUE) : null;

			$use_case = new UpdateCourseUseCase();
			$use_case->execute($id, $title, $base_price, $description, $category_id);

			$this->session->set_flashdata('success', 'Curso atualizado com sucesso.');
			redirect('admin/cursos');
		}

		$data = [
			'page_name' => 'admin/courses/form',
			'title'     => 'Editar Curso',
			'course'    => $course,
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Delete course action.
	 *
	 * @param int $id Course ID
	 * @return void
	 */
	public function delete(int $id): void
	{
		$this->course_model->delete($id);
		$this->session->set_flashdata('success', 'Curso excluído com sucesso.');
		redirect('admin/cursos');
	}
}
