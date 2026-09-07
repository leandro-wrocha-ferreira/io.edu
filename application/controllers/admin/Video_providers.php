<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Video Providers Controller (Admin)
 */
class Video_providers extends MY_Controller
{
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('video_provider_model');
	}

	/**
	 * List video providers view.
	 *
	 * @return void
	 */
	public function index(): void
	{
		$data = [
			'page_name' => 'admin/video_providers/index',
			'title'     => 'Provedores de Vídeo',
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Create video provider form and action.
	 *
	 * @return void
	 */
	public function create(): void
	{
		$this->form_validation->set_rules('name', 'Nome do Provedor', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('api_key', 'API Key', 'trim|max_length[255]');

		if ($this->form_validation->run() === TRUE) {
			// @todo: Call CreateVideoProviderUseCase
			
			$this->session->set_flashdata('success', 'Provedor de vídeo cadastrado com sucesso.');
			redirect('admin/provedores-video');
		}

		$data = [
			'page_name' => 'admin/video_providers/form',
			'title'     => 'Novo Provedor de Vídeo',
			'provider'  => null,
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Edit video provider form and action.
	 *
	 * @param int $id Provider ID
	 * @return void
	 */
	public function edit(int $id): void
	{
		$this->form_validation->set_rules('name', 'Nome do Provedor', 'required|trim|max_length[255]');
		$this->form_validation->set_rules('api_key', 'API Key', 'trim|max_length[255]');

		if ($this->form_validation->run() === TRUE) {
			// @todo: Call UpdateVideoProviderUseCase
			
			$this->session->set_flashdata('success', 'Provedor de vídeo atualizado com sucesso.');
			redirect('admin/provedores-video');
		}

		// Mock object
		$provider = new stdClass();
		$provider->id = $id;
		$provider->name = 'Vimeo Teste';
		$provider->api_key = 'some_api_key_123';

		$data = [
			'page_name' => 'admin/video_providers/form',
			'title'     => 'Editar Provedor de Vídeo',
			'provider'  => $provider,
		];

		$this->load->view('admin/index', $data);
	}

	/**
	 * Delete video provider action.
	 *
	 * @param int $id Provider ID
	 * @return void
	 */
	public function delete(int $id): void
	{
		$this->session->set_flashdata('success', 'Provedor de vídeo excluído com sucesso.');
		redirect('admin/provedores-video');
	}
}
