<?php

namespace app\domain\commercial;

use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing the commercial Product tied 1:1 to a Course.
 */
final class Product
{
	private ?int $id = null;
	private int $course_id;
	private bool $is_free = false;
	private ?Price $price = null;
	private BillingModel $billing_model;
	private InstallmentLimit $max_installments;
	private ?SubscriptionInterval $subscription_interval = null;
	private ?int $max_billing_cycles = null;
	private EnabledPaymentMethods $payment_methods;
	private ?int $payment_configuration_id = null;
	private ?int $cost_center_id = null;
	private ?DateTime $created_at = null;
	private ?DateTime $updated_at = null;
	private ?DateTime $deleted_at = null;

	/**
	 * Create a product domain entity.
	 *
	 * @param int $course_id Associated course ID
	 * @param bool $is_free Flag indicating if product is free
	 * @param Price|null $price Product price (null if free)
	 * @param BillingModel|null $billing_model Billing model (default one_time)
	 * @param InstallmentLimit|null $max_installments Max installments (default 1)
	 * @param SubscriptionInterval|null $subscription_interval Recurring interval
	 * @param int|null $max_billing_cycles Max recurring billing cycles
	 * @param EnabledPaymentMethods|null $payment_methods Allowed payment methods
	 * @param int|null $payment_configuration_id Financial profile FK
	 * @param int|null $cost_center_id Cost center FK
	 * @param int|null $id Product ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 * @throws ValidationException When business invariants are violated
	 */
	public static function create(
		int $course_id,
		bool $is_free = false,
		?Price $price = null,
		?BillingModel $billing_model = null,
		?InstallmentLimit $max_installments = null,
		?SubscriptionInterval $subscription_interval = null,
		?int $max_billing_cycles = null,
		?EnabledPaymentMethods $payment_methods = null,
		?int $payment_configuration_id = null,
		?int $cost_center_id = null,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		if ($course_id <= 0) {
			throw new ValidationException("Course ID must be greater than 0");
		}

		$product = new self();
		$product->id = $id;
		$product->course_id = $course_id;
		$product->created_at = $created_at ?? new DateTime();
		$product->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$product->deleted_at = $deleted_at;

		$product->apply_conditions(
			$is_free,
			$price,
			$billing_model ?? BillingModel::one_time(),
			$max_installments ?? InstallmentLimit::single(),
			$subscription_interval,
			$max_billing_cycles,
			$payment_methods ?? new EnabledPaymentMethods([]),
			$payment_configuration_id,
			$cost_center_id
		);

		return $product;
	}

	/**
	 * Update product commercial conditions.
	 *
	 * @param bool $is_free Flag indicating if product is free
	 * @param Price|null $price New price (null if free)
	 * @param BillingModel $billing_model Billing model
	 * @param InstallmentLimit $max_installments Max installments
	 * @param SubscriptionInterval|null $subscription_interval Subscription interval
	 * @param int|null $max_billing_cycles Max billing cycles
	 * @param EnabledPaymentMethods $payment_methods Allowed payment methods
	 * @param int|null $payment_configuration_id Financial profile FK
	 * @param int|null $cost_center_id Cost center FK
	 * @return void
	 * @throws ValidationException When conditions are invalid
	 */
	public function update_conditions(
		bool $is_free,
		?Price $price,
		BillingModel $billing_model,
		InstallmentLimit $max_installments,
		?SubscriptionInterval $subscription_interval,
		?int $max_billing_cycles,
		EnabledPaymentMethods $payment_methods,
		?int $payment_configuration_id,
		?int $cost_center_id
	): void
	{
		$this->apply_conditions(
			$is_free,
			$price,
			$billing_model,
			$max_installments,
			$subscription_interval,
			$max_billing_cycles,
			$payment_methods,
			$payment_configuration_id,
			$cost_center_id
		);
		$this->updated_at = new DateTime();
	}

	/**
	 * Apply conditions and enforce domain invariants.
	 *
	 * @param bool $is_free
	 * @param Price|null $price
	 * @param BillingModel $billing_model
	 * @param InstallmentLimit $max_installments
	 * @param SubscriptionInterval|null $subscription_interval
	 * @param int|null $max_billing_cycles
	 * @param EnabledPaymentMethods $payment_methods
	 * @param int|null $payment_configuration_id
	 * @param int|null $cost_center_id
	 * @return void
	 * @throws ValidationException
	 */
	private function apply_conditions(
		bool $is_free,
		?Price $price,
		BillingModel $billing_model,
		InstallmentLimit $max_installments,
		?SubscriptionInterval $subscription_interval,
		?int $max_billing_cycles,
		EnabledPaymentMethods $payment_methods,
		?int $payment_configuration_id,
		?int $cost_center_id
	): void
	{
		$this->is_free = $is_free;

		if ($is_free) {
			// Free product invariant: no installments, no subscription, no payment methods, no financial profile
			$this->price = null;
			$this->billing_model = BillingModel::one_time();
			$this->max_installments = InstallmentLimit::single();
			$this->subscription_interval = null;
			$this->max_billing_cycles = null;
			$this->payment_methods = new EnabledPaymentMethods([]);
			$this->payment_configuration_id = null;
			$this->cost_center_id = $cost_center_id;
			return;
		}

		if ($price === null || $price->is_zero()) {
			throw new ValidationException("Paid products must have a valid non-zero price");
		}

		$this->price = $price;

		// Paid product invariants
		if ($billing_model->is_subscription()) {
			if ($subscription_interval === null) {
				throw new ValidationException("Subscription interval is required for subscription model");
			}
			$this->billing_model = $billing_model;
			$this->max_installments = InstallmentLimit::single();
			$this->subscription_interval = $subscription_interval;
			$this->max_billing_cycles = $max_billing_cycles !== null && $max_billing_cycles > 0 ? $max_billing_cycles : null;
		} else {
			$this->billing_model = BillingModel::one_time();
			$this->max_installments = $max_installments;
			$this->subscription_interval = null;
			$this->max_billing_cycles = null;
		}

		$this->payment_methods = $payment_methods;
		$this->payment_configuration_id = $payment_configuration_id;
		$this->cost_center_id = $cost_center_id;
	}

	/**
	 * Validate if product commercial conditions allow being enabled for sale.
	 *
	 * @return void
	 * @throws ValidationException When conditions are incomplete for paid sale
	 */
	public function validate_sales_eligibility(): void
	{
		if ($this->is_free()) {
			return;
		}

		if ($this->payment_configuration_id === null || $this->payment_configuration_id <= 0) {
			throw new ValidationException("Paid products enabled for sale require an active payment configuration");
		}

		if ($this->payment_methods->is_empty()) {
			throw new ValidationException("At least one payment method is required for paid products");
		}
	}

	/**
	 * Check if product is free.
	 *
	 * @return bool
	 */
	public function is_free(): bool
	{
		return $this->is_free;
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_course_id(): int
	{
		return $this->course_id;
	}

	public function get_price(): ?Price
	{
		return $this->price;
	}

	public function get_billing_model(): BillingModel
	{
		return $this->billing_model;
	}

	public function get_max_installments(): InstallmentLimit
	{
		return $this->max_installments;
	}

	public function get_subscription_interval(): ?SubscriptionInterval
	{
		return $this->subscription_interval;
	}

	public function get_max_billing_cycles(): ?int
	{
		return $this->max_billing_cycles;
	}

	public function get_payment_methods(): EnabledPaymentMethods
	{
		return $this->payment_methods;
	}

	public function get_payment_configuration_id(): ?int
	{
		return $this->payment_configuration_id;
	}

	public function get_cost_center_id(): ?int
	{
		return $this->cost_center_id;
	}

	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}
}
