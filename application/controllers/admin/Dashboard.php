<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller (Admin)
 *
 * Manages the main admin panel page display.
 */
class Dashboard extends CI_Controller
{
    /**
     * Constructor.
     *
     * Loads helpers and libraries needed for the dashboard.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
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
        $data = [
            'page_name' => 'admin/dashboard',
            'user_name' => $this->session->userdata('user_name'),
            'title' => 'Painel Administrativo',
        ];

        $this->load->view('admin/index', $data);
    }
}
