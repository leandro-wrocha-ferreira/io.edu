<?php

namespace app\usecases\course;

use app\domain\commercial\Product;
use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\repositories\ProductPriceVersionRepositoryInterface;
use app\domain\commercial\repositories\ProductRepositoryInterface;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\domain\course\Course;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\repositories\CostCenterRepositoryInterface;
use app\domain\finance\repositories\PaymentConfigurationRepositoryInterface;
use app\infrastructure\database\TransactionInterface;
use DateTime;

/**
 * Use case for creating a course with atomic 1:1 commercial product guarantee.
 */
class CreateCourseUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/** @var CategoryRepositoryInterface */
	private CategoryRepositoryInterface $category_repository;

	/** @var ProductRepositoryInterface */
	private ProductRepositoryInterface $product_repository;

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
	 * @param CourseRepositoryInterface $course_repository
	 * @param CategoryRepositoryInterface $category_repository
	 * @param ProductRepositoryInterface $product_repository
	 * @param TransactionInterface $transaction
	 * @param ProductPriceVersionRepositoryInterface|null $price_version_repository
	 * @param PaymentConfigurationRepositoryInterface|null $payment_configuration_repository
	 * @param CostCenterRepositoryInterface|null $cost_center_repository
	 */
	public function __construct(
		CourseRepositoryInterface $course_repository,
		CategoryRepositoryInterface $category_repository,
		ProductRepositoryInterface $product_repository,
		TransactionInterface $transaction,
		?ProductPriceVersionRepositoryInterface $price_version_repository = null,
		?PaymentConfigurationRepositoryInterface $payment_configuration_repository = null,
		?CostCenterRepositoryInterface $cost_center_repository = null
	)
	{
		$this->course_repository = $course_repository;
		$this->category_repository = $category_repository;
		$this->product_repository = $product_repository;
		$this->transaction = $transaction;
		$this->price_version_repository = $price_version_repository;
		$this->payment_configuration_repository = $payment_configuration_repository;
		$this->cost_center_repository = $cost_center_repository;
	}

	/**
	 * Execute course creation with atomic product creation.
	 *
	 * @param int $category_id Category ID
	 * @param string $title Course title
	 * @param string|null $slug Course slug (auto-generated if null)
	 * @param string $status Status ('draft', 'active', 'inactive', 'archived')
	 * @param string $access_period_type Access period type ('lifetime' or 'limited_time')
	 * @param int|null $access_days Access days (required if limited_time)
	 * @param int|null $workload_in_hours Workload in hours
	 * @param string|null $short_description Short description
	 * @param string|null $description Full description
	 * @param string|null $image Cover image URL/path
	 * @param int $duration_in_seconds Total duration in seconds
	 * @param string|null $objectives Learning objectives
	 * @param string|null $target_audience Target audience
	 * @param string|null $requirements Prerequisites
	 * @param bool $certificate_enabled Certificate emission toggle
	 * @param string|null $image_url External image URL
	 * @param int $video_min_completion_percentage Minimum video percentage required for completion
	 * @param bool $is_available_for_sale Commercial availability for sale
	 * @param int $price_in_cents Commercial price in integer cents
	 * @param string $billing_model 'one_time' or 'subscription'
	 * @param int $max_installments Maximum installments
	 * @param string|null $subscription_interval Subscription interval
	 * @param int|null $max_billing_cycles Maximum billing cycles
	 * @param array $payment_methods Enabled payment methods
	 * @param int|null $payment_configuration_id Financial profile ID
	 * @param int|null $cost_center_id Cost center ID
	 * @param int|null $created_by Authenticated user ID responsible
	 * @param string $currency Currency code
	 * @return Course
	 * @throws NotFoundException When category does not exist
	 * @throws ConflictException When slug is already taken
	 * @throws ValidationException When invariants are violated
	 */
	public function execute(
		int $category_id,
		string $title,
		?string $slug = null,
		string $status = CourseStatus::DRAFT,
		string $access_period_type = CourseAccessPeriod::TYPE_LIMITED_TIME,
		?int $access_days = 365,
		?int $workload_in_hours = null,
		?string $short_description = null,
		?string $description = null,
		?string $image = null,
		int $duration_in_seconds = 0,
		?string $objectives = null,
		?string $target_audience = null,
		?string $requirements = null,
		bool $certificate_enabled = true,
		?string $image_url = null,
		int $video_min_completion_percentage = 80,
		bool $is_available_for_sale = false,
		bool $is_free = true,
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
	): Course
	{
		$category = $this->category_repository->find_by_id($category_id);
		if ($category === null) {
			throw new NotFoundException("Category not found");
		}

		$has_id_placeholder = !empty($slug) && CourseSlug::has_id_placeholder($slug);
		$base_slug = $has_id_placeholder ? CourseSlug::extract_base_slug($slug) : null;

		if (!$has_id_placeholder) {
			$course_slug = !empty($slug) ? new CourseSlug($slug) : new CourseSlug($title);
			$existing = $this->course_repository->find_by_slug($course_slug);
			if ($existing !== null) {
				throw new ConflictException("Course slug already exists");
			}
		} else {
			$temp_name = ($base_slug !== null && $base_slug !== '') ? $base_slug : CourseSlug::slugify($title);
			$course_slug = new CourseSlug('temp-' . bin2hex(random_bytes(6)) . '-' . ($temp_name !== '' ? $temp_name : 'curso'));
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

		$access_period = new CourseAccessPeriod($access_period_type, $access_days);
		$course_status = new CourseStatus($status);
		$workload = $workload_in_hours !== null ? new Workload($workload_in_hours) : null;

		$course = Course::create(
			$category_id,
			$title,
			$course_slug,
			$course_status,
			$access_period,
			$workload,
			$short_description,
			$description,
			$image,
			$duration_in_seconds,
			$objectives,
			$target_audience,
			$requirements,
			$certificate_enabled,
			null,
			null,
			null,
			null,
			null,
			$image_url,
			$video_min_completion_percentage,
			$is_available_for_sale
		);

		return $this->transaction->run(function () use (
			$course,
			$has_id_placeholder,
			$base_slug,
			$is_free,
			$price_in_cents,
			$billing_model,
			$max_installments,
			$subscription_interval,
			$max_billing_cycles,
			$payment_methods,
			$payment_configuration_id,
			$cost_center_id,
			$created_by,
			$currency
		) {
			$persisted_course = $this->course_repository->create($course);

			if ($has_id_placeholder) {
				$final_base = ($base_slug !== null && $base_slug !== '') ? $base_slug : $persisted_course->get_title();
				$final_slug = CourseSlug::format_with_id($persisted_course->get_id(), $final_base);
				$existing = $this->course_repository->find_by_slug($final_slug);
				if ($existing !== null && $existing->get_id() !== $persisted_course->get_id()) {
					throw new ConflictException("Course slug already exists");
				}
				$persisted_course->set_slug($final_slug);
				$persisted_course = $this->course_repository->save($persisted_course);
			}

			$price = (!$is_free && $price_in_cents !== null) ? Price::from_cents($price_in_cents, $currency) : null;
			$billing_model_vo = BillingModel::from_string($billing_model);
			$max_installments_vo = InstallmentLimit::from_int($max_installments);
			$interval_vo = $subscription_interval !== null ? SubscriptionInterval::from_string($subscription_interval) : null;
			$methods_vo = new EnabledPaymentMethods($payment_methods);

			$product = Product::create(
				$persisted_course->get_id(),
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

			if ($persisted_course->is_available_for_sale()) {
				$product->validate_sales_eligibility();
			}

			$persisted_product = $this->product_repository->save($product);

			if (!$persisted_product->is_free() && $this->price_version_repository !== null) {
				$snapshot = [
					'product_id' => $persisted_product->get_id(),
					'course_id' => $persisted_product->get_course_id(),
					'course_title' => $persisted_course->get_title(),
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

			return $persisted_course;
		});
	}
}
