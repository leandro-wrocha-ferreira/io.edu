<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\AppException;

/**
 * Base Controller for the Application.
 *
 * Intercepts method invocation via _remap() to provide global exception handling
 * for both JSON (AJAX / X-App-Json) and HTML (Flashdata / Error Views) requests.
 */
class MY_Controller extends CI_Controller
{
	/**
	 * Intercept all controller method calls.
	 *
	 * @param string $method Target method name
	 * @param array $params Action parameters from URI
	 * @return mixed
	 */
	public function _remap($method, $params = [])
	{
		if (!method_exists($this, $method)) {
			show_404();
			return;
		}

		try {
			return call_user_func_array([$this, $method], $params);
		} catch (AppException $e) {
			$this->handle_app_exception($e);
		} catch (\Throwable $e) {
			$this->handle_generic_exception($e);
		}
	}

	/**
	 * Determine if current request expects a JSON response.
	 *
	 * @return bool
	 */
	protected function wants_json(): bool
	{
		if (function_exists('wants_json_response')) {
			return wants_json_response();
		}

		$app_json = $this->input->get_request_header('X-App-Json', TRUE);
		if ($app_json !== null && stripos($app_json, 'application/json') !== false) {
			return true;
		}

		return $this->input->is_ajax_request();
	}

	/**
	 * Handle domain and application exceptions (AppException and subclasses).
	 *
	 * Uses the exception's getStatusCode() (e.g. 404, 422, 409, 401, 403)
	 * and error details for JSON responses, or sets flashdata and redirects for HTML.
	 *
	 * @param AppException $e
	 * @return void
	 */
	protected function handle_app_exception(AppException $e): void
	{
		if ($this->wants_json()) {
			$response = [
				'error' => true,
				'message' => $e->getMessage(),
			];

			$errors = $e->getErrors();
			if (!empty($errors)) {
				$response['errors'] = $errors;
			}

			json_response($response, $e->getStatusCode());
			return;
		}

		$this->session->set_flashdata('error', $e->getMessage());
		redirect($this->get_redirect_back_url());
	}

	/**
	 * Handle unexpected server errors and uncaught throwables (HTTP 500).
	 *
	 * @param \Throwable $e
	 * @return void
	 * @throws \Throwable
	 */
	protected function handle_generic_exception(\Throwable $e): void
	{
		log_message('error', $e->getMessage() . "\n" . $e->getTraceAsString());

		if ($this->wants_json()) {
			json_response([
				'error' => true,
				'message' => 'Ocorreu um erro interno no servidor.',
			], 500);
			return;
		}

		if (ENVIRONMENT === 'development') {
			throw $e;
		}

		show_error('Ocorreu um erro inesperado ao processar sua solicitação.', 500);
	}

	/**
	 * Get fallback URL for redirection after errors.
	 *
	 * @return string
	 */
	protected function get_redirect_back_url(): string
	{
		$referer = $this->input->server('HTTP_REFERER');
		return $referer ?: site_url('admin/painel');
	}
}
