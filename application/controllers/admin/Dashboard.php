<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index()
    {
        $data = [
            'user_name' => $this->session->userdata('user_name'),
            'title' => 'Painel Administrativo',
        ];

        $this->load->view('admin/dashboard', $data);
    }
}
