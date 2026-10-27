<?php

namespace app\usecases\commercial;

use app\domain\commercial\Product;
use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\repositories\ProductPriceVersionRepositoryInterface;
use app\domain\commercial\repositories\ProductRepositoryInterface;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\repositories\CostCenterRepositoryInterface;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\infrastructure\database\TransactionInterface;
use DateTime;

/**
 * Use case for creating a commercial product tied 1:1 to a course.
 */
class CreateProductUseCase
{
	/** @var ProductRepositoryInterface */
	private ProductRepositoryInterface $product_repository;

	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/** @var TransactionInterface */
	private TransactionInterface $transaction;

	/** @var ProductPriceVersionRepositoryInterface|null */
	private ?ProductPriceVersionRepositoryInterface $price_version_repository;

	/** @var PaymentConfigurationRepositoryInterface|null */
	private ?PaymentConfigurationRepositoryInterface $payment_configuration_repository;

	/** @var CostCenterRepositoryInterface|null */
	private ?CostCenterRepositoryInterface $cost_center_repository;

	/**
	 * Constructor.
	 *
	 * @param ProductRepositoryInterface $product_repository
	 * @param CourseRepositoryInterface $course_repository
	 * @param TransactionInterface $transaction
	 * @param ProductPriceVersionRepositoryInterface|null $price_version_repository
	 * @param PaymentConfigurationRepositoryInterface|null $payment_configuration_repository
	 * @param CostCenterRepositoryInterface|null $cost_center_repository
	 */
	public function __construct(
		ProductRepositoryInterface $product_repository,
		CourseRepositoryInterface $course_repository,
		TransactionInterface $transaction,
		?ProductPriceVersionRepositoryInterface $price_version_repository = null,
		?PaymentConfigurationRepositoryInterface $payment_configuration_repository = null,
		?CostCenterRepositoryInterface $cost_center_repository = null
	)
	{
		$this->product_repository = $product_repository;
		$this->course_repository = $course_repository;
		$this->transaction = $transaction;
		$this->price_version_repository = $price_version_repository;
		$this->payment_configuration_repository = $payment_configuration_repository;
		$this->cost_center_repository = $cost_center_repository;
	}

	/**
	 * Execute product creation.
	 *
	 * @param int $course_id Associated course ID
	 * @param bool $is_free Whether the product is free
	 * @param int|null $price_in_cents Price in integer cents (null if free)
	 * @param string $billing_model 'one_time' or 'subscription'
	 * @param int $max_installments Maximum installments allowed
	 * @param string|null $subscription_interval Subscription interval
	 * @param int|null $max_billing_cycles Maximum billing cycles
	 * @param array $payment_methods List of payment methods
	 * @param int|null $payment_configuration_id Financial configuration ID
	 * @param int|null $cost_center_id Cost center ID
	 * @param int|null $created_by Authenticated user ID
	 * @param string $currency Currency code
	 * @return Product
	 * @throws NotFoundException When course is not found
	 * @throws ConflictException When course already has a product
	 * @throws ValidationException When business invariants are violated
	 */
	public function execute(
		int $course_id,
		bool $is_free = false,
		?int $price_in_cents = null,
		string $billing_model = BillingModel::ONE_TIME,
		int $max_installments = 1,
		?string $subscription_interval = null,
		?int $max_billing_cycles = null,
		array $payment_methods = [],
		?int $payment_configuration_id = null,
		?int $cost_center_id = null,
		?int $created_by = null,
		string $currency = 'BRL'
	): Product
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		$existing_product = $this->product_repository->find_by_course_id($course_id);
		if ($existing_product !== null) {
			throw new ConflictException("Product already exists for this course");
		}

		if ($payment_configuration_id !== null && $this->payment_configuration_repository !== null) {
			$payment_config = $this->payment_configuration_repository->find_by_id($payment_configuration_id);
			if ($payment_config === null || !$payment_config->is_active()) {
				throw new ValidationException("Payment configuration not found or inactive");
			}
		}

		if ($cost_center_id !== null && $this->cost_center_repository !== null) {
			$cost_center = $this->cost_center_repository->find_by_id($cost_center_id);
			if ($cost_center === null || !$cost_center->is_active()) {
				throw new ValidationException("Cost center not found or inactive");
			}
		}

		$price = (!$is_free && $price_in_cents !== null) ? Price::from_cents($price_in_cents, $currency) : null;
		$billing_model_vo = BillingModel::from_string($billing_model);
		$max_installments_vo = InstallmentLimit::from_int($max_installments);
		$interval_vo = $subscription_interval !== null ? SubscriptionInterval::from_string($subscription_interval) : null;
		$methods_vo = new EnabledPaymentMethods($payment_methods);

		$product = Product::create(
			$course_id,
			$is_free,
			$price,
			$billing_model_vo,
			$max_installments_vo,
			$interval_vo,
			$max_billing_cycles,
			$methods_vo,
			$payment_configuration_id,
			$cost_center_id
		);

		return $this->transaction->run(function () use (
			$product,
			$course,
			$created_by,
			$currency
		) {
			$persisted_product = $this->product_repository->save($product);

			if (!$persisted_product->is_free() && $this->price_version_repository !== null) {
				$snapshot = [
					'product_id' => $persisted_product->get_id(),
					'course_id' => $persisted_product->get_course_id(),
					'course_title' => $course->get_title(),
					'is_free' => 0,
					'price_in_cents' => $persisted_product->get_price()->get_cents(),
					'currency' => $persisted_product->get_price()->get_currency(),
					'billing_model' => (string) $persisted_product->get_billing_model(),
					'max_installments' => $persisted_product->get_max_installments()->get_limit(),
					'subscription_interval' => $persisted_product->get_subscription_interval() !== null ? (string) $persisted_product->get_subscription_interval() : null,
					'max_billing_cycles' => $persisted_product->get_max_billing_cycles(),
					'payment_methods' => $persisted_product->get_payment_methods()->to_array(),
					'payment_configuration_id' => $persisted_product->get_payment_configuration_id(),
					'cost_center_id' => $persisted_product->get_cost_center_id(),
					'timestamp' => (new DateTime())->format('Y-m-d H:i:s'),
				];

				$version = ProductPriceVersion::create(
					$persisted_product->get_id(),
					1,
					null,
					$persisted_product->get_price(),
					$snapshot,
					'Initial price',
					$created_by,
					false,
					null,
					null,
					$currency
				);
				$this->price_version_repository->save($version);
			}

			return $persisted_product;
		});
	}
}
