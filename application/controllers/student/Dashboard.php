<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller (Student Area)
 *
 * Manages the student authenticated views: dashboard, learning journeys,
 * and virtual classroom / course player.
 */
class Dashboard extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Student panel home page.
	 *
	 * Loads the student dashboard view with priority on "continue learning",
	 * progress metrics, and enrolled courses.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'user_name' => $this->session->userdata('user_name') ?? 'Estudante',
			'title' => 'Meu Painel — ' . get_institution_name(),
			'page_name' => 'student/dashboard',
		];

		$this->load->view('layout/student', $data);
	}

	/**
	 * Student journeys / career tracks view.
	 *
	 * Displays regulatory deadlines, milestones, and structured paths.
	 *
	 * @return void
	 */
	public function journeys()
	{
		$data = [
			'user_name' => $this->session->userdata('user_name') ?? 'Estudante',
			'title' => 'Trilhas & Jornadas — ' . get_institution_name(),
			'page_name' => 'student/journeys',
		];

		$this->load->view('layout/student', $data);
	}

	/**
	 * Virtual classroom / video player view.
	 *
	 * Provides the lecture viewer, syllabus playlist, and learning resources.
	 *
	 * @param int|string $course_id Identifier of the course
	 * @return void
	 */
	public function classroom($course_id = 1)
	{
		$data = [
			'user_name' => $this->session->userdata('user_name') ?? 'Estudante',
			'title' => 'Sala de Aula Virtual — ' . get_institution_name(),
			'page_name' => 'student/classroom',
			'course_id' => $course_id,
		];

		$this->load->view('layout/student', $data);
	}
}
