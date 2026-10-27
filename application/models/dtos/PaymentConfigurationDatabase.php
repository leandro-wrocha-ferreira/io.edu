<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw payment configuration record.
 */
final class PaymentConfigurationDatabase
{
	public int $id;
	public string $name;
	public int $payment_account_id;
	public array $allowed_methods;
	public ?string $description;
	public int $is_active;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;
	public ?string $account_name = null;
	public ?string $account_identifier = null;
	public ?string $environment = null;
	public ?string $provider_name = null;
	public ?string $provider_slug = null;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->name = (string) $data['name'];
		$this->payment_account_id = (int) $data['payment_account_id'];

		$raw_methods = $data['allowed_methods'] ?? [];
		if (is_string($raw_methods)) {
			$decoded = json_decode($raw_methods, true);
			$this->allowed_methods = is_array($decoded) ? $decoded : [];
		} elseif (is_array($raw_methods)) {
			$this->allowed_methods = $raw_methods;
		} else {
			$this->allowed_methods = [];
		}

		$this->description = isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null;
		$this->is_active = !empty($data['is_active']) ? 1 : 0;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;
		$this->account_name = isset($data['account_name']) ? (string) $data['account_name'] : null;
		$this->account_identifier = isset($data['account_identifier']) ? (string) $data['account_identifier'] : null;
		$this->environment = isset($data['environment']) ? (string) $data['environment'] : null;
		$this->provider_name = isset($data['provider_name']) ? (string) $data['provider_name'] : null;
		$this->provider_slug = isset($data['provider_slug']) ? (string) $data['provider_slug'] : null;
	}
}
