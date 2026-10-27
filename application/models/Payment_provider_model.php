<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\finance\PaymentProvider;
use app\domain\finance\repositories\PaymentProviderRepositoryInterface;
use app\models\dtos\PaymentProviderDatabase;
use app\models\mappers\PaymentProviderMapper;

/**
 * Payment provider model implementing PaymentProviderRepositoryInterface.
 */
class Payment_provider_model extends MY_Model implements PaymentProviderRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'payment_providers';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a payment provider by its ID.
	 *
	 * @param int $id
	 * @return PaymentProvider|null
	 */
	public function find_by_id(int $id): ?PaymentProvider
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return PaymentProviderMapper::to_entity(new PaymentProviderDatabase($row));
	}

	/**
	 * Find a payment provider by its unique slug.
	 *
	 * @param string $slug
	 * @return PaymentProvider|null
	 */
	public function find_by_slug(string $slug): ?PaymentProvider
	{
		$row = $this->db->from($this->table)
			->where('slug', $slug)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return PaymentProviderMapper::to_entity(new PaymentProviderDatabase($row));
	}

	/**
	 * Find all active payment providers.
	 *
	 * @return array<PaymentProvider>
	 */
	public function find_all_active(): array
	{
		$rows = $this->db->from($this->table)
			->where('is_active', 1)
			->order_by('name', 'ASC')
			->get()
			->result_array();

		return PaymentProviderMapper::to_entities($rows);
	}
}
