<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\factories\ModelFactory;
use app\usecases\course\CreateCourseLessonUseCase;
use app\usecases\course\DeleteCourseLessonUseCase;
use app\usecases\course\GetCourseDetailUseCase;
use app\usecases\course\GetCourseLessonUseCase;
use app\usecases\course\ListCourseModulesUseCase;
use app\usecases\course\ReorderCourseLessonsUseCase;
use app\usecases\course\UpdateCourseLessonUseCase;

/**
 * Course Lessons Administration Controller
 *
 * Handles creation, content editing, deletion, and reordering of course lessons.
 */
class Lessons extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Interactive lesson and blocks editor view.
	 *
	 * @param int|null $course_id Course identifier
	 * @param int|null $lesson_id Optional lesson identifier
	 * @return void
	 * @throws NotFoundException
	 */
	public function editor($course_id = null, $lesson_id = null)
	{
		$course_id = (int) $course_id;
		if ($course_id <= 0) {
			throw new NotFoundException('Course not found');
		}

		$course_model = ModelFactory::make('course_model');
		$module_model = ModelFactory::make('course_module_model');
		$lesson_model = ModelFactory::make('course_lesson_model');
		$block_model = ModelFactory::make('lesson_block_model');

		$detail_use_case = new GetCourseDetailUseCase($course_model);
		$course = $detail_use_case->execute($course_id);

		$list_modules_use_case = new ListCourseModulesUseCase($module_model, $course_model);
		$modules = $list_modules_use_case->execute($course_id);

		$lesson = null;
		if ($lesson_id !== null && is_numeric($lesson_id)) {
			$get_lesson_use_case = new GetCourseLessonUseCase($lesson_model, $block_model);
			$lesson = $get_lesson_use_case->execute((int) $lesson_id);
		}

		if ($this->input->method() === 'post') {
			$this->form_validation->set_rules('title', 'Título da Aula', 'required|trim|min_length[3]|max_length[255]');
			$this->form_validation->set_rules('module_id', 'Módulo', 'required|numeric');

			if ($this->form_validation->run() === TRUE) {
				$target_module_id = (int) $this->input->post('module_id');
				$title = (string) $this->input->post('title');
				$description = $this->input->post('description') ? (string) $this->input->post('description') : null;
				$status = $this->input->post('status') ? (string) $this->input->post('status') : 'draft';

				if ($lesson !== null && $lesson->get_id() !== null) {
					$update_lesson_use_case = new UpdateCourseLessonUseCase($lesson_model);
					$update_lesson_use_case->execute(
						(int) $lesson->get_id(),
						$title,
						$description,
						$status
					);
					$this->session->set_flashdata('success', 'Aula atualizada com sucesso!');
				} else {
					$create_lesson_use_case = new CreateCourseLessonUseCase($lesson_model, $module_model);
					$created_lesson = $create_lesson_use_case->execute(
						$target_module_id,
						$title,
						$description,
						null,
						$status
					);
					$this->session->set_flashdata('success', 'Aula criada com sucesso!');
					redirect('admin/cursos/' . $course_id . '/aulas/editor/' . $created_lesson->get_id());
					return;
				}
			}
		}

		$data = [
			'title' => $lesson !== null ? 'Editor de Aula — ' . $lesson->get_title() : 'Nova Aula — ' . $course->get_title(),
			'page_name' => 'admin/courses/lesson_editor',
			'course' => $course,
			'modules' => $modules,
			'lesson' => $lesson,
			'page_js' => [
				'admin/courses/lesson-uploader.js',
				'admin/courses/lesson-blocks-reorder.js',
				'admin/courses/lesson_editor.js',
			],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * AJAX endpoint to create or update a lesson.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function save()
	{
		$lesson_id = $this->get_input('lesson_id') ? (int) $this->get_input('lesson_id') : null;
		$inputs = (array) $this->get_input();

		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('title', 'Título da Aula', 'required|trim|min_length[3]|max_length[255]');
		$this->form_validation->set_rules('status', 'Status', 'trim|in_list[draft,published,archived]');

		if ($lesson_id === null) {
			$this->form_validation->set_rules('module_id', 'Módulo', 'required|is_natural_no_zero');
		}

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$module_id = (int) $this->get_input('module_id');
		$title = (string) $this->get_input('title');
		$description = $this->get_input('description') ? (string) $this->get_input('description') : null;
		$status = $this->get_input('status') ? (string) $this->get_input('status') : 'draft';

		$module_model = ModelFactory::make('course_module_model');
		$lesson_model = ModelFactory::make('course_lesson_model');

		if ($lesson_id !== null && $lesson_id > 0) {
			$use_case = new UpdateCourseLessonUseCase($lesson_model);
			$lesson = $use_case->execute($lesson_id, $title, $description, $status);
		} else {
			$use_case = new CreateCourseLessonUseCase($lesson_model, $module_model);
			$lesson = $use_case->execute($module_id, $title, $description, null, $status);
		}

		json_response([
			'success' => true,
			'message' => 'Aula salva com sucesso!',
			'lesson' => [
				'id' => $lesson->get_id(),
				'module_id' => $lesson->get_module_id(),
				'title' => $lesson->get_title(),
				'description' => $lesson->get_description(),
				'sort_order' => $lesson->get_sort_order(),
				'status' => $lesson->get_status(),
			],
		]);
	}

	/**
	 * Delete a lesson.
	 *
	 * @param int|null $lesson_id
	 * @return void
	 * @throws ValidationException
	 */
	public function delete($lesson_id = null)
	{
		$lesson_id = (int) $lesson_id;
		if ($lesson_id <= 0) {
			throw new ValidationException('Lesson ID is required');
		}

		$lesson_model = ModelFactory::make('course_lesson_model');
		$use_case = new DeleteCourseLessonUseCase($lesson_model);
		$use_case->execute($lesson_id);

		if ($this->input->is_ajax_request()) {
			json_response(['success' => true, 'message' => 'Aula excluída com sucesso!']);
			return;
		}

		$this->session->set_flashdata('success', 'Aula excluída com sucesso!');
		redirect($this->agent->is_referral() ? $this->agent->referrer() : 'admin/cursos');
	}

	/**
	 * AJAX endpoint to reorder lessons in a module.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function reorder()
	{
		$inputs = (array) $this->get_input();
		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('module_id', 'Módulo', 'required|is_natural_no_zero');

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$module_id = (int) $this->get_input('module_id');
		$lesson_ids = $this->get_input('lesson_ids');
		if (!is_array($lesson_ids)) {
			$lesson_ids = [];
		}
		$lesson_ids = array_map('intval', $lesson_ids);

		$module_model = ModelFactory::make('course_module_model');
		$lesson_model = ModelFactory::make('course_lesson_model');
		$use_case = new ReorderCourseLessonsUseCase($lesson_model, $module_model);
		$use_case->execute($module_id, $lesson_ids);

		json_response(['success' => true, 'message' => 'Ordem das aulas atualizada com sucesso!']);
	}

	/**
	 * AJAX endpoint to get lesson data with blocks.
	 *
	 * @param int|null $lesson_id
	 * @return void
	 * @throws NotFoundException
	 */
	public function ajax_data($lesson_id = null)
	{
		$lesson_id = (int) $lesson_id;
		if ($lesson_id <= 0) {
			throw new NotFoundException('Lesson not found');
		}

		$lesson_model = ModelFactory::make('course_lesson_model');
		$block_model = ModelFactory::make('lesson_block_model');

		$use_case = new GetCourseLessonUseCase($lesson_model, $block_model);
		$lesson = $use_case->execute($lesson_id);

		$blocks_data = [];
		foreach ($lesson->get_blocks() as $block) {
			$blocks_data[] = [
				'id' => $block->get_id(),
				'lesson_id' => $block->get_lesson_id(),
				'type' => $block->get_type(),
				'payload' => $block->get_payload(),
				'sort_order' => $block->get_sort_order(),
				'is_required' => $block->is_required(),
			];
		}

		json_response([
			'success' => true,
			'lesson' => [
				'id' => $lesson->get_id(),
				'module_id' => $lesson->get_module_id(),
				'title' => $lesson->get_title(),
				'description' => $lesson->get_description(),
				'sort_order' => $lesson->get_sort_order(),
				'status' => $lesson->get_status(),
				'blocks' => $blocks_data,
			],
		]);
	}
}
