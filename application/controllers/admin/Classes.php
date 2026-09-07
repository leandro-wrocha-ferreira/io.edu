<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classes Controller (Admin)
 */
class Classes extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('class_model');
		$this->load->model('course_model');
	}

	/**
	 * List classes view.
	 *
	 * @return void
	 */
	public function index(): void
	{
		$data = [
			'page_name' => 'admin/classes/index',
			'title'     => 'Gestão de Turmas',
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Create class form and action.
	 *
	 * @return void
	 */
	public function create(): void
	{
		$this->form_validation->set_rules('name', 'Nome da Turma', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('course_id', 'Curso', 'required|integer');

		if ($this->form_validation->run() === TRUE) {
			// @todo: Call CreateClassUseCase
			
			$this->session->set_flashdata('success', 'Turma criada com sucesso.');
			redirect('admin/turmas');
		}

		$data = [
			'page_name' => 'admin/classes/form',
			'title'     => 'Nova Turma',
			'class_obj' => null,
			'courses'   => [], // Mock
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Edit class form and action.
	 *
	 * @param int $id Class ID
	 * @return void
	 */
	public function edit(int $id): void
	{
		$this->form_validation->set_rules('name', 'Nome da Turma', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('course_id', 'Curso', 'required|integer');

		if ($this->form_validation->run() === TRUE) {
			// @todo: Call UpdateClassUseCase
			
			$this->session->set_flashdata('success', 'Turma atualizada com sucesso.');
			redirect('admin/turmas');
		}

		// Mock object
		$class_obj = new stdClass();
		$class_obj->id = $id;
		$class_obj->name = 'Turma 1 - 2026';
		$class_obj->course_id = 1;

		$data = [
			'page_name' => 'admin/classes/form',
			'title'     => 'Editar Turma',
			'class_obj' => $class_obj,
			'courses'   => [], // Mock
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Delete class action.
	 *
	 * @param int $id Class ID
	 * @return void
	 */
	public function delete(int $id): void
	{
		$this->session->set_flashdata('success', 'Turma excluída com sucesso.');
		redirect('admin/turmas');
	}
}
