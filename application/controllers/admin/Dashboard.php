<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\admin\GetAdminDashboardMetricsUseCase;
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
		$metrics_use_case = new GetAdminDashboardMetricsUseCase(
			ModelFactory::make('user_model'),
			ModelFactory::make('role_model')
		);

		$metrics = $metrics_use_case->execute();

		$data = [
			'page_name' => 'admin/dashboard',
			'user_name' => $this->session->userdata('user_name'),
			'title' => 'Painel Administrativo',
			'metrics' => $metrics,
		];

		$this->load->view('layout/admin', $data);
	}
}
