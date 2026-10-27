<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw product record.
 */
final class ProductDatabase
{
	public int $id;
	public int $course_id;
	public int $is_free;
	public ?int $price_in_cents;
	public string $currency;
	public string $billing_model;
	public int $max_installments;
	public ?string $subscription_interval;
	public ?int $max_billing_cycles;
	public array $payment_methods;
	public ?int $payment_configuration_id;
	public ?int $cost_center_id;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->course_id = (int) $data['course_id'];
		$this->is_free = !empty($data['is_free']) ? 1 : 0;
		$this->price_in_cents = isset($data['price_in_cents']) && $data['price_in_cents'] !== null && $data['price_in_cents'] !== '' ? (int) $data['price_in_cents'] : null;
		$this->currency = (string) ($data['currency'] ?? 'BRL');
		$this->billing_model = (string) ($data['billing_model'] ?? 'one_time');
		$this->max_installments = isset($data['max_installments']) ? (int) $data['max_installments'] : 1;
		$this->subscription_interval = isset($data['subscription_interval']) && $data['subscription_interval'] !== '' ? (string) $data['subscription_interval'] : null;
		$this->max_billing_cycles = isset($data['max_billing_cycles']) && $data['max_billing_cycles'] !== '' && $data['max_billing_cycles'] !== null ? (int) $data['max_billing_cycles'] : null;

		$methods = $data['payment_methods'] ?? [];
		if (is_string($methods)) {
			$decoded = json_decode($methods, true);
			$this->payment_methods = is_array($decoded) ? $decoded : [];
		} elseif (is_array($methods)) {
			$this->payment_methods = $methods;
		} else {
			$this->payment_methods = [];
		}

		$this->payment_configuration_id = isset($data['payment_configuration_id']) && $data['payment_configuration_id'] !== '' && $data['payment_configuration_id'] !== null ? (int) $data['payment_configuration_id'] : null;
		$this->cost_center_id = isset($data['cost_center_id']) && $data['cost_center_id'] !== '' && $data['cost_center_id'] !== null ? (int) $data['cost_center_id'] : null;
		$this->created_at = !empty($data['created_at']) ? new DateTime((string) $data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime((string) $data['deleted_at']) : null;
	}
}
