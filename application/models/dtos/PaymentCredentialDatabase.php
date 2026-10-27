<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw payment credential record.
 */
final class PaymentCredentialDatabase
{
	public int $id;
	public int $provider_id;
	public string $name;
	public string $environment;
	public string $api_key_encrypted;
	public ?string $webhook_secret_encrypted;
	public int $is_active;
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
		$this->name = (string) $data['name'];
		$this->environment = (string) ($data['environment'] ?? 'sandbox');
		$this->api_key_encrypted = (string) $data['api_key_encrypted'];
		$this->webhook_secret_encrypted = isset($data['webhook_secret_encrypted']) && $data['webhook_secret_encrypted'] !== '' ? (string) $data['webhook_secret_encrypted'] : null;
		$this->is_active = !empty($data['is_active']) ? 1 : 0;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
	}
}
