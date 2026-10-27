<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\finance\PaymentCredential;
use app\domain\finance\repositories\PaymentCredentialRepositoryInterface;
use app\models\dtos\PaymentCredentialDatabase;
use app\models\mappers\PaymentCredentialMapper;

/**
 * Payment credential model implementing PaymentCredentialRepositoryInterface.
 */
class Payment_credential_model extends MY_Model implements PaymentCredentialRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'payment_credentials';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a payment credential by its ID.
	 *
	 * @param int $id
	 * @return PaymentCredential|null
	 */
	public function find_by_id(int $id): ?PaymentCredential
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return PaymentCredentialMapper::to_entity(new PaymentCredentialDatabase($row));
	}

	/**
	 * Find credentials by provider ID.
	 *
	 * @param int $provider_id
	 * @return array<PaymentCredential>
	 */
	public function find_by_provider_id(int $provider_id): array
	{
		$rows = $this->db->from($this->table)
			->where('provider_id', $provider_id)
			->order_by('name', 'ASC')
			->get()
			->result_array();

		return PaymentCredentialMapper::to_entities($rows);
	}

	/**
	 * Create a new credential record.
	 *
	 * @param PaymentCredential $credential
	 * @return PaymentCredential
	 */
	public function create(PaymentCredential $credential): PaymentCredential
	{
		$data = PaymentCredentialMapper::to_database_create($credential);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $credential;
	}

	/**
	 * Save a payment credential.
	 *
	 * @param PaymentCredential $credential
	 * @return PaymentCredential
	 */
	public function save(PaymentCredential $credential): PaymentCredential
	{
		if ($credential->get_id() === null) {
			return $this->create($credential);
		}

		$data = PaymentCredentialMapper::to_database_update($credential);
		$this->update($data, [$this->primary_key => $credential->get_id()]);

		return $this->find_by_id($credential->get_id()) ?? $credential;
	}

	/**
	 * Deactivate/delete a payment credential.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool
	{
		return (bool) $this->update([
			'is_active' => 0,
		], ['id' => $id]);
	}
}
