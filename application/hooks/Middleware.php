<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Middleware
{
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
            redirect(base_url('login'));
        }

        if ($uri === 'admin' && $CI->session->userdata('user_role') !== 'admin') {
            show_404();
        }

        if ($uri === 'aluno' && $CI->session->userdata('user_role') !== 'student') {
            show_404();
        }
    }
}
