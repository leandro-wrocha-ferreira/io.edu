<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\CountStudentsUseCase;
use app\factories\ModelFactory;

/**
 * Dashboard Controller (Admin)
 *
 * Manages the main admin panel page display.
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
	 * Admin panel home page.
	 *
	 * Loads the sidebar layout and the dynamic dashboard content.
	 *
	 * @return void
	 */
	public function index()
	{
		$count_use_case = new CountStudentsUseCase(ModelFactory::make('user_model'));

		$data = [
			'page_name' => 'admin/dashboard',
			'user_name' => $this->session->userdata('user_name'),
			'title' => 'Painel Administrativo',
			'total_students' => $count_use_case->execute(),
		];

		$this->load->view('admin/index', $data);
	}
}
