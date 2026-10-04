<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classes Cohort Administration Controller
 *
 * Handles management of student cohorts/classes, detailed monitoring,
 * and cohort creation/edition prototypes.
 */
class Classes extends MY_Controller
{
	/**
	 * List all class cohorts with filters and metrics.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'title' => 'Gerenciamento de Turmas',
			'page_name' => 'admin/classes/index',
			'classes' => get_mock_admin_classes(),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display detail view for a specific class cohort.
	 *
	 * @param int $class_id Class identifier
	 * @return void
	 */
	public function detail($class_id = 1)
	{
		$class_id = (int) $class_id;
		$cohort = get_mock_class_by_id($class_id);

		$data = [
			'title' => $cohort['name'],
			'page_name' => 'admin/classes/detail',
			'cohort' => $cohort,
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display creation form for a new class cohort.
	 *
	 * @return void
	 */
	public function create()
	{
		$data = [
			'title' => 'Nova Turma',
			'page_name' => 'admin/classes/form',
			'courses' => get_mock_admin_courses(),
		];

		$this->load->view('layout/admin', $data);
	}
}
