<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Evaluations Administration Controller
 *
 * Handles management of evaluations, tests, quizzes, question authoring,
 * and grade tracking prototypes.
 */
class Evaluations extends MY_Controller
{
	/**
	 * List all evaluations with filters and metrics.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'title' => 'Gerenciamento de Avaliações',
			'page_name' => 'admin/evaluations/index',
			'evaluations' => get_mock_admin_evaluations(),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display detail view for a specific evaluation.
	 *
	 * @param int $evaluation_id Evaluation identifier
	 * @return void
	 */
	public function detail($evaluation_id = 1)
	{
		$evaluation_id = (int) $evaluation_id;
		$evaluation = get_mock_evaluation_by_id($evaluation_id);

		$data = [
			'title' => $evaluation['title'],
			'page_name' => 'admin/evaluations/detail',
			'evaluation' => $evaluation,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display authoring/editing form for an evaluation.
	 *
	 * @param int|null $evaluation_id Optional evaluation ID
	 * @return void
	 */
	public function create($evaluation_id = null)
	{
		$data = [
			'title' => 'Nova Avaliação',
			'page_name' => 'admin/evaluations/form',
			'evaluation' => null,
			'courses' => get_mock_admin_courses(),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display authoring/editing form for an existing evaluation.
	 *
	 * @param int $evaluation_id Evaluation ID
	 * @return void
	 */
	public function edit($evaluation_id = 1)
	{
		$evaluation_id = (int) $evaluation_id;
		$evaluation = get_mock_evaluation_by_id($evaluation_id);

		$data = [
			'title' => 'Editar Avaliação — ' . $evaluation['title'],
			'page_name' => 'admin/evaluations/form',
			'evaluation' => $evaluation,
			'courses' => get_mock_admin_courses(),
		];

		$this->load->view('layout/admin', $data);
	}
}
