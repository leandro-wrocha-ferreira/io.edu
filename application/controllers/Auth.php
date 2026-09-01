<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\identity\AuthenticateUserUseCase;

/**
 * Authentication Controller
 *
 * Handles user login, logout, and role-based redirection.
 */
class Auth extends CI_Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display login form or process login submission.
     *
     * If user is already logged in, redirects to their panel.
     * On POST, validates credentials via AuthenticateUserUseCase
     * and creates the session on success.
     *
     * @return void
     */
    public function login()
    {
        if ($this->session->userdata('user_id')) {
            $this->_redirect_by_role();
            return;
        }

        if ($this->input->method() === 'post') {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            try {
                $use_case = new AuthenticateUserUseCase();
                $user = $use_case->execute($email, $password);

                $session_data = [
                    'user_id' => $user->get_id(),
                    'user_name' => $user->get_name(),
                    'user_email' => (string) $user->get_email(),
                    'user_role' => $user->get_role(),
                    'logged_in' => TRUE,
                ];
                $this->session->set_userdata($session_data);

                $this->_redirect_by_role();
            } catch (\Exception $e) {
                log_message('error', $e->getMessage());
                $this->session->set_flashdata('error', $e->getMessage());
                redirect(base_url('entrar'));
            }
        }

        $this->load->view('login', ['title' => 'Login']);
    }

    /**
     * Log out the current user.
     *
     * Destroys the session and redirects to the login page.
     *
     * @return void
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('entrar'));
    }

    /**
     * Redirect user to their role-specific panel.
     *
     * @return void
     */
    private function _redirect_by_role()
    {
        $role = $this->session->userdata('user_role');
        if ($role === 'admin-master' || $role === 'admin') {
            redirect(base_url('admin/painel'));
        } else {
            redirect(base_url('aluno/painel'));
        }
    }
}
