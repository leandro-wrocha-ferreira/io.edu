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
