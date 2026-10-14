<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\ValidationException;
use app\factories\ModelFactory;
use app\usecases\course\CreateCourseModuleUseCase;
use app\usecases\course\DeleteCourseModuleUseCase;
use app\usecases\course\ReorderCourseModulesUseCase;
use app\usecases\course\UpdateCourseModuleUseCase;

/**
 * Course Modules Administration Controller
 *
 * Handles creation, update, deletion, and reordering of course modules.
 */
class Modules extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * AJAX endpoint to create or update a module.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function save()
	{
		$module_id = $this->get_input('module_id') ? (int) $this->get_input('module_id') : null;
		$inputs = (array) $this->get_input();

		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('title', 'Título do Módulo', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'trim|in_list[draft,published,archived]');

		if ($module_id === null) {
			$this->form_validation->set_rules('course_id', 'Curso', 'required|is_natural_no_zero');
		}

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$course_id = (int) $this->get_input('course_id');
		$title = (string) $this->get_input('title');
		$description = $this->get_input('description') ? (string) $this->get_input('description') : null;
		$status = $this->get_input('status') ? (string) $this->get_input('status') : 'draft';

		$course_model = ModelFactory::make('course_model');
		$module_model = ModelFactory::make('course_module_model');

		if ($module_id !== null && $module_id > 0) {
			$use_case = new UpdateCourseModuleUseCase($module_model);
			$module = $use_case->execute($module_id, $title, $description, $status);
		} else {
			$use_case = new CreateCourseModuleUseCase($module_model, $course_model);
			$module = $use_case->execute($course_id, $title, $description, null, $status);
		}

		json_response([
			'success' => true,
			'message' => 'Módulo salvo com sucesso!',
			'module' => [
				'id' => $module->get_id(),
				'course_id' => $module->get_course_id(),
				'title' => $module->get_title(),
				'description' => $module->get_description(),
				'sort_order' => $module->get_sort_order(),
				'status' => $module->get_status(),
			],
		]);
	}

	/**
	 * Delete a module.
	 *
	 * @param int|null $module_id
	 * @return void
	 * @throws ValidationException
	 */
	public function delete($module_id = null)
	{
		$module_id = (int) $module_id;
		if ($module_id <= 0) {
			throw new ValidationException('Module ID is required');
		}

		$module_model = ModelFactory::make('course_module_model');
		$use_case = new DeleteCourseModuleUseCase($module_model);
		$use_case->execute($module_id);

		if ($this->input->is_ajax_request()) {
			json_response(['success' => true, 'message' => 'Módulo excluído com sucesso!']);
			return;
		}

		$this->session->set_flashdata('success', 'Módulo excluído com sucesso!');
		redirect($this->agent->is_referral() ? $this->agent->referrer() : 'admin/cursos');
	}

	/**
	 * AJAX endpoint to reorder modules in a course.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function reorder()
	{
		$inputs = (array) $this->get_input();
		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('course_id', 'Curso', 'required|is_natural_no_zero');

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$course_id = (int) $this->get_input('course_id');
		$module_ids = $this->get_input('module_ids');
		if (!is_array($module_ids)) {
			$module_ids = [];
		}
		$module_ids = array_map('intval', $module_ids);

		$course_model = ModelFactory::make('course_model');
		$module_model = ModelFactory::make('course_module_model');
		$use_case = new ReorderCourseModulesUseCase($module_model, $course_model);
		$use_case->execute($course_id, $module_ids);

		json_response(['success' => true, 'message' => 'Ordem dos módulos atualizada com sucesso!']);
	}
}
