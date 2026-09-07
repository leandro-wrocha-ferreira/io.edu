<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\video\VideoProvider;
use app\domain\education\video\VideoProviderRepositoryInterface;

/**
 * Video Provider Model implementing VideoProviderRepositoryInterface.
 */
class Video_provider_model extends MY_Model implements VideoProviderRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'video_providers';

	/**
	 * Entity class for hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = VideoProvider::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a video provider by ID.
	 *
	 * @param int|string $id Provider ID
	 * @return VideoProvider|null
	 */
	public function find_by_id($id): ?VideoProvider
	{
		return parent::find_by_id($id);
	}

	/**
	 * Save a VideoProvider entity (insert or update).
	 *
	 * @param VideoProvider $provider
	 * @return int
	 */
	public function save(VideoProvider $provider): int
	{
		$data = [
			'name'        => $provider->get_name(),
			'driver'      => $provider->get_driver(),
			'credentials' => $provider->get_credentials() ? json_encode($provider->get_credentials()) : null,
		];

		if ($provider->get_id() !== null) {
			$this->update($data, ['id' => $provider->get_id()]);
			return $provider->get_id();
		}

		$id = (int) $this->insert($data);
		$provider->set_id($id);
		return $id;
	}


}
