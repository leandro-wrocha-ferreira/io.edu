<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('json_response')) {
	/**
	 * Send a standardized JSON response.
	 *
	 * @param mixed $data Data to be JSON-encoded
	 * @param int $status_code HTTP status code (default: 200)
	 * @return void
	 */
	function json_response($data, int $status_code = 200): void
	{
		$CI =& get_instance();
		$CI->output
			->set_status_header($status_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}

if (!function_exists('wants_json_response')) {
	/**
	 * Determine if the incoming request expects a JSON response.
	 *
	 * Checks for:
	 * 1. Custom 'X-App-Json: application/json' header.
	 * 2. CI3 AJAX request detection ('X-Requested-With: XMLHttpRequest').
	 * 3. Standard 'Accept: application/json' header.
	 *
	 * @return bool
	 */
	function wants_json_response(): bool
	{
		$CI =& get_instance();

		$app_json = $CI->input->get_request_header('X-App-Json', TRUE);
		if ($app_json !== null && stripos($app_json, 'application/json') !== false) {
			return true;
		}

		if ($CI->input->is_ajax_request()) {
			return true;
		}

		$accept = $CI->input->get_request_header('Accept', TRUE);
		if ($accept !== null && stripos($accept, 'application/json') !== false) {
			return true;
		}

		return false;
	}
}
