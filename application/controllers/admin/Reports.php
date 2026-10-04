<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Analytical Reports Controller
 *
 * Handles presentation dashboards for Academic and Financial reports.
 * Uses isolated presentation fixtures for high-fidelity UI demonstration.
 */
class Reports extends MY_Controller
{
	/**
	 * Display Academic analytical dashboard.
	 *
	 * @return void
	 */
	public function academic()
	{
		$data = [
			'title' => 'Relatórios Acadêmicos',
			'page_name' => 'admin/reports/academic',
			'reports' => get_mock_academic_reports(),
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Display Financial analytical dashboard.
	 *
	 * @return void
	 */
	public function financial()
	{
		$data = [
			'title' => 'Relatórios Financeiros',
			'page_name' => 'admin/reports/financial',
			'reports' => get_mock_financial_reports(),
		];

		$this->load->view('layout/admin', $data);
	}
}
