<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\AppException;
use app\domain\identity\constants\RoleSlug;
use app\usecases\identity\AuthenticateUserUseCase;
use app\factories\ModelFactory;

/**
 * Authentication Controller
 *
 * Handles user login, logout, and role-based redirection.
 */
class Auth extends MY_Controller
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
		}

		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === TRUE) {
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$use_case = new AuthenticateUserUseCase(ModelFactory::make('user_model'));
			$user = $use_case->execute($email, $password);

			$role = $user->get_role();
			if (!$role) {
				throw new AppException('User has no role');
			}

			$session_data = [
				'user_id' => $user->get_id(),
				'user_name' => $user->get_name(),
				'user_email' => (string) $user->get_email(),
				'user_role' => $role,
				'is_admin' => $role === RoleSlug::ADMIN,
				'logged_in' => TRUE,
			];
			$this->session->set_userdata($session_data);

			$this->_redirect_by_role();
			return;
		}

		$data = [
			'page_name' => 'auth/login_form',
			'title' => 'Entrar — ' . get_institution_name(),
		];

		$this->load->view('layout/auth', $data);
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
		redirect('entrar');
	}

	/**
	 * Redirect user to their role-specific panel.
	 *
	 * @return void
	 */
	private function _redirect_by_role()
	{
		$role = $this->session->userdata('user_role');
		if ($role === RoleSlug::ADMIN) {
			redirect('admin/painel');
		} else {
			redirect('aluno/painel');
		}
	}
}
