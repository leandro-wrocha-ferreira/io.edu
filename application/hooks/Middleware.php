<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication and RBAC Middleware
 *
 * Checks if the user is authenticated and has the appropriate role
 * to access protected routes (admin, student).
 *
 * Hook registered in post_controller_constructor.
 */
class Middleware
{
    /**
     * Validate access to the current route.
     *
     * Public routes (entrar, sair, welcome, migrations) are allowed without authentication.
     * CLI requests bypass authentication entirely.
     * All other routes require login. Admin and student routes check the role.
     *
     * @return void
     */
    public function validate()
    {
        if (is_cli()) {
            return;
        }

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
