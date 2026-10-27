<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw payment account record.
 */
final class PaymentAccountDatabase
{
	public int $id;
	public int $provider_id;
	public int $credential_id;
	public string $name;
	public string $environment;
	public ?string $account_identifier;
	public ?string $document_number;
	public string $status;
	public DateTime $created_at;
	public ?DateTime $updated_at;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->provider_id = (int) $data['provider_id'];
		$this->credential_id = (int) $data['credential_id'];
		$this->name = (string) $data['name'];
		$this->environment = (string) ($data['environment'] ?? 'sandbox');
		$this->account_identifier = isset($data['account_identifier']) && $data['account_identifier'] !== '' ? (string) $data['account_identifier'] : null;
		$this->document_number = isset($data['document_number']) && $data['document_number'] !== '' ? (string) $data['document_number'] : null;
		$this->status = (string) ($data['status'] ?? 'active');
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
	}
}
