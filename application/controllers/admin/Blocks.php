<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\ValidationException;
use app\factories\ModelFactory;
use app\usecases\course\CreateLessonBlockUseCase;
use app\usecases\course\DeleteLessonBlockUseCase;
use app\usecases\course\ReorderLessonBlocksUseCase;
use app\usecases\course\UpdateLessonBlockUseCase;

/**
 * Lesson Blocks Administration Controller
 *
 * Handles creation, update, deletion, and reordering of lesson content blocks.
 */
class Blocks extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * AJAX endpoint to create or update a lesson content block.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function save()
	{
		$block_id = $this->get_input('block_id') ? (int) $this->get_input('block_id') : null;
		$inputs = (array) $this->get_input();

		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('type', 'Tipo do Bloco', 'required|trim|in_list[video,text,attachment,quiz]');
		if ($block_id === null) {
			$this->form_validation->set_rules('lesson_id', 'Aula', 'required|is_natural_no_zero');
		}

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$lesson_id = (int) $this->get_input('lesson_id');
		$type = (string) $this->get_input('type');
		$is_required = $this->get_input('is_required') !== null ? (bool) $this->get_input('is_required') : true;
		$raw_payload = $this->get_input('payload');
		$payload = is_array($raw_payload) ? $raw_payload : (json_decode((string) $raw_payload, true) ?? []);

		// Validate payload requirements according to block type
		if ($type === 'video' && empty(trim((string) ($payload['url'] ?? '')))) {
			throw new ValidationException('Validation failed', ['url' => 'Video URL cannot be empty']);
		}
		if ($type === 'text' && empty(trim((string) ($payload['content'] ?? '')))) {
			throw new ValidationException('Validation failed', ['content' => 'Text block content cannot be empty']);
		}
		if ($type === 'attachment') {
			if (empty(trim((string) ($payload['filename'] ?? '')))) {
				throw new ValidationException('Validation failed', ['filename' => 'Attachment filename cannot be empty']);
			}
			if (empty(trim((string) ($payload['filepath'] ?? '')))) {
				throw new ValidationException('Validation failed', ['filepath' => 'Attachment filepath cannot be empty']);
			}
		}

		$lesson_model = ModelFactory::make('course_lesson_model');
		$block_model = ModelFactory::make('lesson_block_model');

		if ($block_id !== null && $block_id > 0) {
			$use_case = new UpdateLessonBlockUseCase($block_model);
			$block = $use_case->execute($block_id, $type, $payload, null, $is_required);
		} else {
			$use_case = new CreateLessonBlockUseCase($block_model, $lesson_model);
			$block = $use_case->execute($lesson_id, $type, $payload, null, $is_required);
		}

		json_response([
			'success' => true,
			'message' => 'Bloco salvo com sucesso!',
			'block' => [
				'id' => $block->get_id(),
				'lesson_id' => $block->get_lesson_id(),
				'type' => $block->get_type(),
				'payload' => $block->get_payload(),
				'sort_order' => $block->get_sort_order(),
				'is_required' => $block->is_required(),
			],
		]);
	}

	/**
	 * Delete a lesson block.
	 *
	 * @param int|null $block_id
	 * @return void
	 * @throws ValidationException
	 */
	public function delete($block_id = null)
	{
		$block_id = (int) $block_id;
		if ($block_id <= 0) {
			throw new ValidationException('Block ID is required');
		}

		$block_model = ModelFactory::make('lesson_block_model');
		$use_case = new DeleteLessonBlockUseCase($block_model);
		$use_case->execute($block_id);

		json_response(['success' => true, 'message' => 'Bloco de conteúdo excluído com sucesso!']);
	}

	/**
	 * AJAX endpoint to reorder blocks within a lesson.
	 *
	 * @return void
	 * @throws ValidationException
	 */
	public function reorder()
	{
		$inputs = (array) $this->get_input();
		$this->form_validation->set_data($inputs);
		$this->form_validation->set_rules('lesson_id', 'Aula', 'required|is_natural_no_zero');

		if ($this->form_validation->run() !== TRUE) {
			throw new ValidationException('Validation failed', $this->form_validation->error_array());
		}

		$lesson_id = (int) $this->get_input('lesson_id');
		$block_ids = $this->get_input('block_ids');
		if (!is_array($block_ids)) {
			$block_ids = [];
		}
		$block_ids = array_map('intval', $block_ids);

		$lesson_model = ModelFactory::make('course_lesson_model');
		$block_model = ModelFactory::make('lesson_block_model');
		$use_case = new ReorderLessonBlocksUseCase($block_model, $lesson_model);
		$use_case->execute($lesson_id, $block_ids);

		json_response(['success' => true, 'message' => 'Ordem dos blocos atualizada com sucesso!']);
	}
}
