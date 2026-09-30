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
		} catch (AppException $exception) {
			$this->handle_app_exception($exception);
		} catch (\Throwable $exception) {
			$this->handle_generic_exception($exception);
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
	 * Translate exception message according to the active user language.
	 *
	 * Looks up direct phrase or exception key in loaded language files.
	 * Falls back safely to the canonical English message if translation is missing.
	 *
	 * @param string $message English message or exception key
	 * @return string Translated message or original fallback
	 */
	public function translate_exception_message(string $message): string
	{
		if ($message === '') {
			return $message;
		}

		// 1. Direct language line lookup
		$translated = $this->lang->line($message);
		if ($translated !== false && $translated !== '') {
			return $translated;
		}

		// 2. Normalized key lookup (e.g. "User not found" -> "exception_user_not_found")
		$normalized_key = 'exception_' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($message)));
		$translated_key = $this->lang->line($normalized_key);
		if ($translated_key !== false && $translated_key !== '') {
			return $translated_key;
		}

		return $message;
	}

	/**
	 * Handle domain and application exceptions (AppException and subclasses).
	 *
	 * Uses the exception's getStatusCode() (e.g. 404, 422, 409, 401, 403)
	 * and error details for JSON responses, or sets flashdata and redirects for HTML.
	 * Translates messages according to the user's detected locale.
	 *
	 * @param AppException $exception
	 * @return void
	 */
	protected function handle_app_exception(AppException $exception): void
	{
		$translated_message = $this->translate_exception_message($exception->getMessage());

		if ($this->wants_json()) {
			$response = [
				'error' => true,
				'message' => $translated_message,
			];

			$errors = $exception->getErrors();
			if (!empty($errors)) {
				$translated_errors = [];
				foreach ($errors as $field => $field_error) {
					if (is_string($field_error)) {
						$translated_errors[$field] = $this->translate_exception_message($field_error);
					} else {
						$translated_errors[$field] = $field_error;
					}
				}
				$response['errors'] = $translated_errors;
			}

			json_response($response, $exception->getStatusCode());
			return;
		}

		$this->session->set_flashdata('error', $translated_message);
		redirect($this->get_redirect_back_url());
	}

	/**
	 * Handle unexpected server errors and uncaught throwables (HTTP 500).
	 *
	 * @param \Throwable $exception
	 * @return void
	 * @throws \Throwable
	 */
	protected function handle_generic_exception(\Throwable $exception): void
	{
		log_message('error', $exception->getMessage() . "\n" . $exception->getTraceAsString());

		$internal_error_message = $this->lang->line('exception_internal_server_error');
		if ($internal_error_message === false || $internal_error_message === '') {
			$internal_error_message = 'An internal server error occurred.';
		}

		if ($this->wants_json()) {
			json_response([
				'error' => true,
				'message' => $internal_error_message,
			], 500);
			return;
		}

		if (ENVIRONMENT === 'development') {
			throw $exception;
		}

		$unexpected_error_message = $this->lang->line('exception_unexpected_error');
		if ($unexpected_error_message === false || $unexpected_error_message === '') {
			$unexpected_error_message = 'An unexpected error occurred while processing your request.';
		}

		show_error($unexpected_error_message, 500);
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
