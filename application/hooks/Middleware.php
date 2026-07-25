<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Middleware de Autenticação e RBAC
 *
 * Verifica se o usuário está autenticado e se possui a role adequada
 * para acessar as rotas protegidas (admin, aluno).
 *
 * Hook registrado em post_controller_constructor.
 */
class Middleware
{
    /**
     * Valida o acesso à rota atual.
     *
     * Rotas públicas (entrar, sair, welcome) são permitidas sem autenticação.
     * Demais rotas exigem login. As rotas admin e aluno verificam a role.
     *
     * @return void
     */
    public function validate()
    {
        $CI =& get_instance();
        $CI->load->library('session');

        $uri = $CI->uri->segment(1);
        $public_routes = ['entrar', 'sair', 'welcome'];

        if (in_array($uri, $public_routes)) {
            return;
        }

        if (!$CI->session->userdata('logged_in')) {
            redirect(base_url('entrar'));
        }

        if ($uri === 'admin' && $CI->session->userdata('user_role') !== 'admin') {
            show_404();
        }

        if ($uri === 'aluno' && $CI->session->userdata('user_role') !== 'student') {
            show_404();
        }
    }
}
