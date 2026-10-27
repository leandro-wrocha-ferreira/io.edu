<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw payment provider record.
 */
final class PaymentProviderDatabase
{
	public int $id;
	public string $name;
	public string $slug;
	public array $supported_methods;
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
		$this->name = (string) $data['name'];
		$this->slug = (string) $data['slug'];

		$raw_methods = $data['supported_methods'] ?? [];
		if (is_string($raw_methods)) {
			$decoded = json_decode($raw_methods, true);
			$this->supported_methods = is_array($decoded) ? $decoded : [];
		} elseif (is_array($raw_methods)) {
			$this->supported_methods = $raw_methods;
		} else {
			$this->supported_methods = [];
		}

		$this->is_active = !empty($data['is_active']) ? 1 : 0;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
	}
}
