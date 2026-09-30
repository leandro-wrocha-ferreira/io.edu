<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\authorization\constants\PermissionSlug;
use app\domain\identity\constants\RoleSlug;

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
        'admin/painel' => PermissionSlug::DASHBOARD_VIEW,

        // Usuários
        'admin/usuarios' => PermissionSlug::USERS_VIEW,
        'admin/usuarios/dados' => PermissionSlug::USERS_VIEW,
        'admin/usuarios/novo' => PermissionSlug::USERS_CREATE,
        'admin/usuarios/editar' => PermissionSlug::USERS_EDIT,
        'admin/usuarios/ativar' => PermissionSlug::USERS_TOGGLE_STATUS,
        'admin/usuarios/desativar' => PermissionSlug::USERS_TOGGLE_STATUS,
        'admin/usuarios/excluir' => PermissionSlug::USERS_DELETE,

        // Perfis
        'admin/perfis' => PermissionSlug::ROLES_VIEW,
        'admin/perfis/dados' => PermissionSlug::ROLES_VIEW,
        'admin/perfis/novo' => PermissionSlug::ROLES_CREATE,
        'admin/perfis/editar' => PermissionSlug::ROLES_EDIT,
        'admin/perfis/excluir' => PermissionSlug::ROLES_DELETE,
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
        $public_routes = ['entrar', 'sair'];

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
            if ($user_role !== RoleSlug::STUDENT) {
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
