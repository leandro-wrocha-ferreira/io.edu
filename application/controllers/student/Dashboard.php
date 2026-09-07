<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller (Student)
 *
 * Manages the student panel page display.
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
	 * Loads the student dashboard view with user data.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'user_name' => $this->session->userdata('user_name'),
			'title'     => 'Painel do Aluno',
		];

		$this->load->view('student/dashboard', $data);
	}
}
