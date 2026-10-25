<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\factories\LibraryFactory;
use app\factories\ModelFactory;
use app\usecases\finance\CreatePaymentConfigurationUseCase;
use app\usecases\finance\DeletePaymentConfigurationUseCase;
use app\usecases\finance\GetPaymentConfigurationDetailUseCase;
use app\usecases\finance\ListActivePaymentProvidersUseCase;
use app\usecases\finance\ListPaginatedPaymentConfigurationsUseCase;
use app\usecases\finance\UpdatePaymentConfigurationUseCase;

/**
 * Payment Configurations Controller (Admin)
 *
 * Manages gateway payment configurations, credentials, accounts and DataTables listing.
 */
class Payment_configurations extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * List payment configurations view.
	 *
	 * @return void
	 */
	public function index()
	{
		$data = [
			'page_name' => 'admin/payment_configurations/index',
			'title' => 'Configurações de Pagamento',
			'page_js' => ['admin/payment_configurations/index.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Server-side DataTables endpoint for payment configurations.
	 *
	 * @return void
	 */
	public function ajax_data()
	{
		$draw = (int) $this->input->get('draw');
		$start = (int) $this->input->get('start');
		$length = (int) $this->input->get('length');
		if ($length <= 0) {
			$length = 25;
		}

		$search = $this->input->get('search');
		$search_value = is_array($search) ? ($search['value'] ?? '') : '';

		$order = $this->input->get('order');
		$order_col_index = is_array($order) && isset($order[0]['column']) ? (int) $order[0]['column'] : 0;
		$order_dir = is_array($order) && isset($order[0]['dir']) ? $order[0]['dir'] : 'desc';

		$col_map = ['id', 'name', 'provider_name', 'status', 'created_at'];
		$order_col = $col_map[$order_col_index] ?? 'id';

		$payment_config_model = ModelFactory::make('payment_configuration_model');
		$use_case = new ListPaginatedPaymentConfigurationsUseCase($payment_config_model);
		$result = $use_case->execute($start, $length, $search_value, $order_col, $order_dir);

		$data = [];
		foreach ($result['data'] as $configuration) {
			$is_active = $configuration->is_active();
			$status_badge = $is_active
				? '<span class="edu-badge edu-badge-success edu-badge-dot">Ativo</span>'
				: '<span class="edu-badge edu-badge-neutral edu-badge-dot">Inativo</span>';

			$environment = $configuration->get_environment();
			$env_badge = $environment === 'production'
				? '<span class="edu-badge edu-badge-success">Produção</span>'
				: '<span class="edu-badge edu-badge-warning">Sandbox</span>';

			$methods = $configuration->get_allowed_methods();
			$methods_html = '<div class="d-flex flex-wrap gap-1">';
			$method_badges = [
				'credit_card' => '<span class="edu-badge edu-badge-neutral small"><i class="bi bi-credit-card-2-front me-1" aria-hidden="true"></i> Cartão</span>',
				'pix' => '<span class="edu-badge edu-badge-neutral small"><i class="bi bi-qr-code-scan me-1" aria-hidden="true"></i> PIX</span>',
				'boleto' => '<span class="edu-badge edu-badge-neutral small"><i class="bi bi-upc-scan me-1" aria-hidden="true"></i> Boleto</span>',
			];
			foreach ($methods as $method) {
				$methods_html .= $method_badges[$method] ?? ('<span class="edu-badge edu-badge-neutral small">' . html_escape(ucfirst($method)) . '</span>');
			}
			$methods_html .= '</div>';

			$edit_url = site_url('admin/configuracoes-pagamento/editar/' . $configuration->get_id());
			$delete_url = site_url('admin/configuracoes-pagamento/excluir/' . $configuration->get_id());

			$actions = '<div class="edu-action-group">'
				. '<a href="' . $edit_url . '" class="edu-action-btn edu-action-btn-edit" title="Editar Configuração" aria-label="Editar ' . html_escape($configuration->get_name()) . '"><i class="bi bi-pencil-fill" aria-hidden="true"></i></a>'
				. '<a href="' . $delete_url . '" class="edu-action-btn edu-action-btn-delete" title="Excluir Configuração" aria-label="Excluir ' . html_escape($configuration->get_name()) . '" onclick="return confirm(\'Deseja realmente excluir esta configuração de pagamento?\');"><i class="bi bi-trash3-fill" aria-hidden="true"></i></a>'
				. '</div>';

			$provider_name = $configuration->get_provider_name() ?? 'Gateway';

			$data[] = [
				'id' => $configuration->get_id(),
				'name' => '<div><strong>' . html_escape($configuration->get_name()) . '</strong>'
					. ($configuration->get_description() ? '<br><small class="text-muted">' . html_escape($configuration->get_description()) . '</small>' : '') . '</div>',
				'provider' => '<div class="d-flex align-items-center gap-2"><span>' . html_escape($provider_name) . '</span>' . $env_badge . '</div>',
				'methods' => $methods_html,
				'status' => $status_badge,
				'actions' => $actions,
			];
		}

		json_response([
			'draw' => $draw,
			'recordsTotal' => $result['recordsTotal'],
			'recordsFiltered' => $result['recordsFiltered'],
			'data' => $data,
		]);
	}

	/**
	 * Show create payment configuration form and process submission.
	 *
	 * @return void
	 */
	public function create()
	{
		if ($this->input->method() === 'post') {
			$this->form_validation->set_rules('name', 'Nome da Configuração', 'required|trim|min_length[2]|max_length[150]');
			$this->form_validation->set_rules('provider_id', 'Provedor de Pagamento', 'required|is_natural_no_zero');
			$this->form_validation->set_rules('environment', 'Ambiente', 'required|in_list[sandbox,production]');
			$this->form_validation->set_rules('api_key', 'Chave de API / Token', 'required|trim');
			$this->form_validation->set_rules('webhook_secret', 'Segredo de Webhook', 'trim|max_length[255]');
			$this->form_validation->set_rules('description', 'Descrição', 'trim|max_length[255]');
			$this->form_validation->set_rules('allowed_methods[]', 'Métodos de Cobrança Permitidos', 'required', [
				'required' => 'Selecione ao menos um método de pagamento permitido.',
			]);

			if ($this->form_validation->run() === TRUE) {
				$allowed_methods = (array) ($this->input->post('allowed_methods') ?: []);

				$use_case = new CreatePaymentConfigurationUseCase(
					ModelFactory::make('payment_configuration_model'),
					ModelFactory::make('payment_account_model'),
					ModelFactory::make('payment_credential_model'),
					ModelFactory::make('payment_provider_model'),
					LibraryFactory::make('credential_encryption_library'),
					ModelFactory::make('transaction_model')
				);

				$use_case->execute(
					$this->input->post('name'),
					(int) $this->input->post('provider_id'),
					$this->input->post('environment'),
					$this->input->post('api_key'),
					$allowed_methods,
					$this->input->post('webhook_secret') ?: null,
					$this->input->post('description') ?: null,
					$this->input->post('is_active') === '1'
				);

				$this->session->set_flashdata('success', 'Configuração de pagamento cadastrada com sucesso!');
				redirect('admin/configuracoes-pagamento');
			}
		}

		$provider_model = ModelFactory::make('payment_provider_model');
		$list_providers_use_case = new ListActivePaymentProvidersUseCase($provider_model);
		$providers = $list_providers_use_case->execute();

		$data = [
			'page_name' => 'admin/payment_configurations/form',
			'title' => 'Nova Configuração de Pagamento',
			'detail' => null,
			'providers' => $providers,
			'page_js' => ['admin/payment_configurations/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Show update payment configuration form and process submission.
	 *
	 * @param int $config_id Payment configuration identifier
	 * @return void
	 */
	public function update(int $config_id)
	{
		if ($this->input->method() === 'post') {
			$this->form_validation->set_rules('name', 'Nome da Configuração', 'required|trim|min_length[2]|max_length[150]');
			$this->form_validation->set_rules('environment', 'Ambiente', 'required|in_list[sandbox,production]');
			$this->form_validation->set_rules('webhook_secret', 'Segredo de Webhook', 'trim|max_length[255]');
			$this->form_validation->set_rules('description', 'Descrição', 'trim|max_length[255]');
			$this->form_validation->set_rules('allowed_methods[]', 'Métodos de Cobrança Permitidos', 'required', [
				'required' => 'Selecione ao menos um método de pagamento permitido.',
			]);

			if ($this->form_validation->run() === TRUE) {
				$allowed_methods = (array) ($this->input->post('allowed_methods') ?: []);

				$api_key = $this->input->post('api_key');
				$clean_api_key = ($api_key !== null && trim($api_key) !== '') ? trim($api_key) : null;

				$config_model = ModelFactory::make('payment_configuration_model');
				$account_model = ModelFactory::make('payment_account_model');
				$credential_model = ModelFactory::make('payment_credential_model');
				$encryption_service = LibraryFactory::make('credential_encryption_library');
				$transaction_model = ModelFactory::make('transaction_model');

				$update_use_case = new UpdatePaymentConfigurationUseCase(
					$config_model,
					$account_model,
					$credential_model,
					$encryption_service,
					$transaction_model
				);

				$update_use_case->execute(
					(int) $config_id,
					$this->input->post('name'),
					$this->input->post('environment'),
					$allowed_methods,
					$clean_api_key,
					$this->input->post('webhook_secret') ?: null,
					$this->input->post('description') ?: null,
					$this->input->post('is_active') === '1'
				);

				$this->session->set_flashdata('success', 'Configuração de pagamento atualizada com sucesso!');
				redirect('admin/configuracoes-pagamento');
			}
		}

		$config_model = ModelFactory::make('payment_configuration_model');
		$account_model = ModelFactory::make('payment_account_model');
		$credential_model = ModelFactory::make('payment_credential_model');
		$provider_model = ModelFactory::make('payment_provider_model');
		$encryption_service = LibraryFactory::make('credential_encryption_library');

		$get_detail_use_case = new GetPaymentConfigurationDetailUseCase(
			$config_model,
			$account_model,
			$credential_model,
			$provider_model,
			$encryption_service
		);
		$detail = $get_detail_use_case->execute((int) $config_id);

		$list_providers_use_case = new ListActivePaymentProvidersUseCase($provider_model);
		$providers = $list_providers_use_case->execute();

		$data = [
			'page_name' => 'admin/payment_configurations/form',
			'title' => 'Editar Configuração — ' . $detail['configuration']->get_name(),
			'detail' => $detail,
			'providers' => $providers,
			'page_js' => ['admin/payment_configurations/form.js'],
		];

		$this->load->view('layout/admin', $data);
	}

	/**
	 * Delete a payment configuration.
	 *
	 * @param int $config_id Payment configuration identifier
	 * @return void
	 */
	public function delete(int $config_id)
	{
		$config_model = ModelFactory::make('payment_configuration_model');
		$use_case = new DeletePaymentConfigurationUseCase($config_model);
		$use_case->execute((int) $config_id);

		$this->session->set_flashdata('success', 'Configuração de pagamento excluída com sucesso!');
		redirect('admin/configuracoes-pagamento');
	}
}
