<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller (Admin)
 *
 * Gerencia a exibição do painel administrativo principal.
 */
class Dashboard extends CI_Controller
{
    /**
     * Construtor
     *
     * Carrega helpers e libraries necessários para o dashboard.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }

    /**
     * Página inicial do painel administrativo.
     *
     * Carrega o layout com sidebar e o conteúdo dinâmico do dashboard.
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
