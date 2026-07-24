<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\usecases\identity\AuthenticateUserUseCase;

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session');
    }

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
                $this->session->set_flashdata('error', $e->getMessage());
                redirect('autenticacao/login');
            }
        }

        $this->load->view('layout/header', ['title' => 'Login']);
        $this->load->view('auth/login');
        $this->load->view('layout/footer');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('autenticacao/login');
    }

    private function _redirect_by_role()
    {
        $role = $this->session->userdata('user_role');
        if ($role === 'admin') {
            redirect('admin/painel');
        } else {
            redirect('aluno/painel');
        }
    }
}
