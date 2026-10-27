<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\finance\PaymentConfiguration;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\models\dtos\PaymentConfigurationDatabase;
use app\models\mappers\PaymentConfigurationMapper;

/**
 * Payment configuration model implementing PaymentConfigurationRepositoryInterface.
 */
class Payment_configuration_model extends MY_Model implements PaymentConfigurationRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'payment_configurations';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a payment configuration by its ID.
	 *
	 * @param int $id
	 * @return PaymentConfiguration|null
	 */
	public function find_by_id(int $id): ?PaymentConfiguration
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->where('deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return PaymentConfigurationMapper::to_entity(new PaymentConfigurationDatabase($row));
	}

	/**
	 * Find all active payment configurations.
	 *
	 * @return array<PaymentConfiguration>
	 */
	public function find_all_active(): array
	{
		$rows = $this->db->from($this->table)
			->where('is_active', 1)
			->where('deleted_at IS NULL', null, false)
			->order_by('name', 'ASC')
			->get()
			->result_array();

		return PaymentConfigurationMapper::to_entities($rows);
	}

	/**
	 * Create a new payment configuration record.
	 *
	 * @param PaymentConfiguration $configuration
	 * @return PaymentConfiguration
	 */
	public function create(PaymentConfiguration $configuration): PaymentConfiguration
	{
		$data = PaymentConfigurationMapper::to_database_create($configuration);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $configuration;
	}

	/**
	 * Save a payment configuration.
	 *
	 * @param PaymentConfiguration $configuration
	 * @return PaymentConfiguration
	 */
	public function save(PaymentConfiguration $configuration): PaymentConfiguration
	{
		if ($configuration->get_id() === null) {
			return $this->create($configuration);
		}

		$data = PaymentConfigurationMapper::to_database_update($configuration);
		$this->update($data, [$this->primary_key => $configuration->get_id()]);

		return $this->find_by_id($configuration->get_id()) ?? $configuration;
	}

	/**
	 * Find payment configurations with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search term
	 * @param string $order_col Order column
	 * @param string $order_dir Order direction
	 * @param int|null $is_active Optional status filter
	 * @return array{data: array<PaymentConfiguration>, recordsFiltered: int}
	 */
	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $is_active = null
	): array
	{
		$total_filtered = $this->_count_paginated($search, $is_active);

		$allowed = ['id', 'name', 'is_active', 'created_at'];
		$column = in_array($order_col, $allowed, true) ? 'payment_configurations.' . $order_col : 'payment_configurations.name';
		$direction = strtoupper($order_dir) === 'DESC' ? 'DESC' : 'ASC';

		$this->db->select('payment_configurations.*, payment_accounts.name as account_name, payment_accounts.account_identifier, payment_accounts.environment, payment_providers.name as provider_name, payment_providers.slug as provider_slug')
			->from($this->table)
			->join('payment_accounts', 'payment_accounts.id = payment_configurations.payment_account_id', 'inner')
			->join('payment_providers', 'payment_providers.id = payment_accounts.provider_id', 'inner')
			->where('payment_configurations.deleted_at IS NULL', null, false)
			->order_by($column, $direction)
			->limit($length, $start);

		if ($is_active !== null) {
			$this->db->where('payment_configurations.is_active', $is_active);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('payment_configurations.name', $search)
				->or_like('payment_configurations.description', $search)
				->or_like('payment_accounts.account_identifier', $search)
				->or_like('payment_providers.name', $search)
				->group_end();
		}

		$rows = $this->db->get()->result_array();

		return [
			'data' => PaymentConfigurationMapper::to_entities($rows),
			'recordsFiltered' => $total_filtered,
		];
	}

	/**
	 * Count filtered payment configurations.
	 *
	 * @param string $search Search query
	 * @param int|null $is_active Status filter
	 * @return int
	 */
	private function _count_paginated(string $search, ?int $is_active = null): int
	{
		$this->db->from($this->table)
			->join('payment_accounts', 'payment_accounts.id = payment_configurations.payment_account_id', 'inner')
			->join('payment_providers', 'payment_providers.id = payment_accounts.provider_id', 'inner')
			->where('payment_configurations.deleted_at IS NULL', null, false);

		if ($is_active !== null) {
			$this->db->where('payment_configurations.is_active', $is_active);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('payment_configurations.name', $search)
				->or_like('payment_configurations.description', $search)
				->or_like('payment_accounts.account_identifier', $search)
				->or_like('payment_providers.name', $search)
				->group_end();
		}

		return (int) $this->db->count_all_results();
	}

	/**
	 * Count total non-deleted payment configurations.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		return (int) $this->db
			->where('deleted_at IS NULL', null, false)
			->from($this->table)
			->count_all_results();
	}

	/**
	 * Soft delete a payment configuration.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool
	{
		return (bool) $this->update([
			'deleted_at' => date('Y-m-d H:i:s'),
		], ['id' => $id]);
	}

	/**
	 * Check if payment configuration is referenced by any product.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function has_linked_products(int $id): bool
	{
		$count = $this->db->from('products')
			->where('payment_configuration_id', $id)
			->where('deleted_at IS NULL', null, false)
			->count_all_results();

		return $count > 0;
	}
}
