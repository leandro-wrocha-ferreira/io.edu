<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\AppException;
use app\domain\exceptions\NotFoundException;

/**
 * Base Controller for the Application.
 *
 * Intercepts method invocation via _remap() to provide global exception handling
 * for both AJAX (JSON) and HTML (Flashdata / Error Views) requests.
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
        } catch (NotFoundException $e) {
            $this->handle_not_found($e);
        } catch (AppException $e) {
            $this->handle_app_exception($e);
        } catch (\Throwable $e) {
            $this->handle_generic_exception($e);
        }
    }

    /**
     * Handle NotFoundException (404 for DB/Domain Entities).
     *
     * @param NotFoundException $e
     * @return void
     */
    protected function handle_not_found(NotFoundException $e): void
    {
        if ($this->input->is_ajax_request()) {
            json_response([
                'error' => true,
                'message' => $e->getMessage(),
            ], 404);
            return;
        }

        $this->session->set_flashdata('error', $e->getMessage());
        redirect($this->get_redirect_back_url());
    }

    /**
     * Handle domain and application exceptions (Validation, Conflict, etc.).
     *
     * @param AppException $e
     * @return void
     */
    protected function handle_app_exception(AppException $e): void
    {
        if ($this->input->is_ajax_request()) {
            json_response([
                'error' => true,
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], $e->getStatusCode());
            return;
        }

        $this->session->set_flashdata('error', $e->getMessage());
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

        if ($this->input->is_ajax_request()) {
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
