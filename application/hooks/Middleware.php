<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication and RBAC Middleware
 *
 * Checks if the user is authenticated, grants total bypass to admin-master,
 * and verifies granular permissions directly from the database for admin users.
 *
 * Hook registered in post_controller_constructor.
 */
class Middleware
{
    /**
     * Map URI segments to permission slugs.
     *
     * @var array
     */
    private array $route_permission_map = [
        'admin/painel' => 'dashboard.view',
        'admin/usuarios' => 'users.view',
        'admin/usuarios/dados' => 'users.view',
        'admin/usuarios/novo' => 'users.create',
        'admin/usuarios/editar' => 'users.edit',
        'admin/usuarios/ativar' => 'users.toggle_status',
        'admin/usuarios/desativar' => 'users.toggle_status',
        'admin/usuarios/excluir' => 'users.delete',
        'admin/perfis' => 'roles.view',
        'admin/perfis/dados' => 'roles.view',
        'admin/perfis/novo' => 'roles.create',
        'admin/perfis/editar' => 'roles.edit',
        'admin/perfis/excluir' => 'roles.delete',
    ];

    /**
     * Validate access to the current route.
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

        $seg1 = $CI->uri->segment(1);
        $public_routes = ['entrar', 'sair', 'welcome'];

        if (in_array($seg1, $public_routes)) {
            return;
        }

        if (!$CI->session->userdata('logged_in')) {
            redirect(base_url('entrar'));
        }

        $user_role = $CI->session->userdata('user_role');
        $user_id = (int) $CI->session->userdata('user_id');

        // Admin-master has total unrestricted access to everything
        if ($user_role === 'admin-master') {
            return;
        }

        if ($seg1 === 'admin') {
            $seg2 = $CI->uri->segment(2) ?? '';
            $seg3 = $CI->uri->segment(3) ?? '';

            $path = trim("admin/{$seg2}/{$seg3}", '/');
            $path2 = trim("admin/{$seg2}", '/');

            $required_permission = $this->route_permission_map[$path]
                ?? $this->route_permission_map[$path2]
                ?? null;

            if ($required_permission !== null) {
                $CI->load->model('user_model');
                if (!$CI->user_model->has_permission($user_id, $required_permission)) {
                    $this->_render_403();
                }
            }
        } elseif ($seg1 === 'aluno') {
            if ($user_role !== 'student') {
                $this->_render_403();
            }
        }
    }

    /**
     * Render 403 Forbidden error page.
     *
     * @return void
     */
    private function _render_403()
    {
        set_status_header(403);
        include APPPATH . 'views/errors/html/error_403.php';
        exit;
    }
}
