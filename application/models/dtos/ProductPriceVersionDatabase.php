<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw product price version record.
 */
final class ProductPriceVersionDatabase
{
	public int $id;
	public int $product_id;
	public int $version_number;
	public int $is_free;
	public ?int $previous_price_in_cents;
	public ?int $new_price_in_cents;
	public string $currency;
	public array $snapshot;
	public ?string $change_reason;
	public ?int $created_by;
	public DateTime $created_at;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->product_id = (int) $data['product_id'];
		$this->version_number = (int) $data['version_number'];
		$this->is_free = !empty($data['is_free']) ? 1 : 0;
		$this->previous_price_in_cents = isset($data['previous_price_in_cents']) && $data['previous_price_in_cents'] !== null && $data['previous_price_in_cents'] !== '' ? (int) $data['previous_price_in_cents'] : null;
		$this->new_price_in_cents = isset($data['new_price_in_cents']) && $data['new_price_in_cents'] !== null && $data['new_price_in_cents'] !== '' ? (int) $data['new_price_in_cents'] : null;
		$this->currency = (string) ($data['currency'] ?? 'BRL');

		$raw_snapshot = $data['snapshot'] ?? [];
		if (is_string($raw_snapshot)) {
			$decoded = json_decode($raw_snapshot, true);
			$this->snapshot = is_array($decoded) ? $decoded : [];
		} elseif (is_array($raw_snapshot)) {
			$this->snapshot = $raw_snapshot;
		} else {
			$this->snapshot = [];
		}

		$this->change_reason = isset($data['change_reason']) && $data['change_reason'] !== '' ? (string) $data['change_reason'] : null;
		$this->created_by = isset($data['created_by']) && $data['created_by'] !== '' && $data['created_by'] !== null ? (int) $data['created_by'] : null;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
	}
}
