<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\finance\PaymentAccount;
use app\domain\finance\repositories\PaymentAccountRepositoryInterface;
use app\models\dtos\PaymentAccountDatabase;
use app\models\mappers\PaymentAccountMapper;

/**
 * Payment account model implementing PaymentAccountRepositoryInterface.
 */
class Payment_account_model extends MY_Model implements PaymentAccountRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'payment_accounts';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a payment account by its ID.
	 *
	 * @param int $id
	 * @return PaymentAccount|null
	 */
	public function find_by_id(int $id): ?PaymentAccount
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return PaymentAccountMapper::to_entity(new PaymentAccountDatabase($row));
	}

	/**
	 * Find accounts by provider ID.
	 *
	 * @param int $provider_id
	 * @return array<PaymentAccount>
	 */
	public function find_by_provider_id(int $provider_id): array
	{
		$rows = $this->db->from($this->table)
			->where('provider_id', $provider_id)
			->order_by('name', 'ASC')
			->get()
			->result_array();

		return PaymentAccountMapper::to_entities($rows);
	}

	/**
	 * Create a new account record.
	 *
	 * @param PaymentAccount $account
	 * @return PaymentAccount
	 */
	public function create(PaymentAccount $account): PaymentAccount
	{
		$data = PaymentAccountMapper::to_database_create($account);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $account;
	}

	/**
	 * Save a payment account.
	 *
	 * @param PaymentAccount $account
	 * @return PaymentAccount
	 */
	public function save(PaymentAccount $account): PaymentAccount
	{
		if ($account->get_id() === null) {
			return $this->create($account);
		}

		$data = PaymentAccountMapper::to_database_update($account);
		$this->update($data, [$this->primary_key => $account->get_id()]);

		return $this->find_by_id($account->get_id()) ?? $account;
	}

	/**
	 * Delete a payment account.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool
	{
		return (bool) $this->update([
			'status' => PaymentAccount::STATUS_SUSPENDED,
		], ['id' => $id]);
	}
}
